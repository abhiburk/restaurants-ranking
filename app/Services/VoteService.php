<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Vote;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

class VoteService
{
    /**
     * Attempt to cast a vote. Returns result array with status and message.
     */
    public function cast(Request $request, Restaurant $restaurant): array
    {
        $visitorId = $request->input('visitor_id'); // from FingerprintJS on frontend
        $userId = $request->user()?->id;
        $ipAddress = $request->ip();
        $votedAt = now();
        $ipHash = hash('sha256', $ipAddress.$votedAt);
        $uaHash = hash('sha256', $request->userAgent().$votedAt);
        $voteSource = $request->input('vote_source', 'url');

        // ── 1. Hard block: duplicate by logged-in user ─────────────────
        if ($userId) {
            $exists = Vote::where('user_id', $userId)
                ->where('restaurant_id', $restaurant->id)
                ->whereDate('voted_at', $votedAt->toDateString())
                ->exists();

            if ($exists) {
                return $this->response('already_voted', 'You have already voted for this restaurant today.');
            }
        }

        // ── 2. Hard block: duplicate by fingerprint ────────────────────
        $fingerprintExists = Vote::where('visitor_id', $visitorId)
            ->where('restaurant_id', $restaurant->id)
            ->where('voted_at', $votedAt)
            ->exists();

        if ($fingerprintExists) {
            return $this->response('already_voted', 'You have already voted for this restaurant today.');
        }

        // ── 3. Hard block: IP flood (same IP > 5 votes for same restaurant today) ──
        $ipVoteCount = Vote::where('ip_hash', $ipHash)
            ->where('restaurant_id', $restaurant->id)
            ->where('voted_at', $votedAt)
            ->count();

        if ($ipVoteCount >= 5) {
            return $this->response('ip_blocked', 'Too many votes from your network for this restaurant today.');
        }

        // ── 4. Fraud signals: collect flags ───────────────────────────
        $isFlagged = false;
        $flagReason = null;
        $isVpn = false;

        // 4a. Velocity burst — more than 50 votes for this restaurant in last 5 min
        $recentVotes = Vote::where('restaurant_id', $restaurant->id)->where('created_at', '>=', now()->subMinutes(5))->count();

        if ($recentVotes >= 50) {
            $isFlagged = true;
            $flagReason = 'velocity_burst';
        }

        // 4b. Geo check — resolve country from IP
        $countryCode = $this->resolveCountry($ipAddress);

        if ($countryCode && $countryCode !== 'IN') {
            $isFlagged = true;
            $flagReason = $flagReason ?? 'geographic_anomaly';
        }

        // 4c. VPN / datacenter IP detection (basic private/reserved range check)
        if ($this->isVpnOrProxy($ipAddress)) {
            $isVpn = true;
            $isFlagged = true;
            $flagReason = $flagReason ?? 'vpn_detected';
        }

        // 5d. Validate the radius using latitude/longitude
        $isFlagged = $isFlagged || $this->isOutOfRadius($request->input('latitude'), $request->input('longitude'), $restaurant);
        if ($isFlagged) {
            $flagReason = $flagReason ?? 'out_of_radius';
        }

        // 5e. Validate if the user is logged in
        // if ($userId === null) {
        //     $isFlagged = true;
        //     $flagReason = $flagReason ?? 'not_logged_in';
        // }

        try {
            $vote = Vote::create([
                'id' => Str::uuid(),
                'restaurant_id' => $restaurant->id,
                'user_id' => $userId,
                'city_id' => $restaurant->city->id,
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
                'amenities' => $request->input('amenities'),
                'voted_at' => $votedAt,
                'visitor_id' => $visitorId,
                'ip_address' => $ipAddress,
                'ip_hash' => $ipHash,
                'user_agent_hash' => $uaHash,
                'source' => $voteSource,
                'country_code' => $countryCode,
                'is_vpn' => $isVpn,
                'is_flagged' => $isFlagged,
                'flag_reason' => $flagReason,
                'is_counted' => true,
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            return $this->response('success', 'Vote cast successfully.', [
                'vote_id' => $vote->id,
                'is_flagged' => $isFlagged,
            ]);
        } catch (QueryException $e) {
            // Unique constraint violation — race condition catch
            // Two requests slipped through the exists() check simultaneously
            if ($e->getCode() === '23000') {
                return $this->response('already_voted', 'You have already voted for this restaurant today.');
            }

            throw $e;
        }
    }

    /**
     * Resolve country code from IP address.
     * Uses stevebauman/location package if installed, falls back gracefully.
     */
    private function resolveCountry(string $ip): ?string
    {
        // Skip for local/private IPs during development
        if ($this->isPrivateIp($ip)) {
            return null;
        }

        try {
            if (class_exists(Location::class)) {
                $location = Location::get($ip);

                return $location?->countryCode ?? null;
            }
        } catch (\Throwable) {
            // Geo lookup failed — don't block the vote
        }

        return null;
    }

    /**
     * Basic VPN / datacenter detection.
     * For production, swap this with an API like ip-api.com or ipqualityscore.com.
     */
    private function isVpnOrProxy(string $ip): bool
    {
        if ($this->isPrivateIp($ip)) {
            return false;
        }

        // Check known proxy headers that suggest a proxy/VPN is in use
        $proxyHeaders = [
            'HTTP_VIA',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED_FOR_IP',
            'VIA',
            'X_FORWARDED_FOR',
        ];

        foreach ($proxyHeaders as $header) {
            if (! empty($_SERVER[$header])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP is a private / loopback / reserved address.
     */
    private function isPrivateIp(string $ip): bool
    {
        return ! filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Consistent response shape for the controller to handle.
     */
    private function response(string $status, string $message, array $data = []): array
    {
        return array_merge(['status' => $status, 'message' => $message], $data);
    }

    private function isOutOfRadius(float $latitude, float $longitude, Restaurant $restaurant): bool
    {
        return $this->distanceInMetersAndKm($latitude, $longitude, $restaurant->latitude, $restaurant->longitude) > $restaurant->radius;
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param  float  $lat1  Latitude of point 1 (in degrees)
     * @param  float  $lon1  Longitude of point 1 (in degrees)
     * @param  float  $lat2  Latitude of point 2 (in degrees)
     * @param  float  $lon2  Longitude of point 2 (in degrees)
     * @return float Distance in meters
     */
    public function distanceInMetersAndKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth radius in meters

        // Convert degrees to radians
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        // Differences
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        // Haversine formula
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distance in meters
        $meters = $earthRadius * $c;

        return round($meters, 2);
    }
}
