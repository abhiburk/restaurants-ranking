<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        $ip = $this->faker->ipv4();
        $votedAt = $this->faker->dateTimeBetween('-8 days', 'today');

        return [
            'id'              => Str::uuid(),
            'restaurant_id'   => Restaurant::active()->inRandomOrder()->factory(),
            'user_id'         => null,
            'voted_at'       => $votedAt,
            'visitor_id'      => Str::uuid(), // each vote gets unique fingerprint by default
            'ip_address'      => $ip,
            'ip_hash'         => hash('sha256', $ip . $votedAt),
            'user_agent_hash' => hash('sha256', $this->faker->userAgent() . $votedAt),
            'vote_source'     => $this->faker->randomElement(['qr_scan', 'qr_scan', 'qr_scan', 'direct_link', 'leaderboard']),
            'country_code'    => $this->faker->randomElement(['IN', 'IN', 'IN', 'IN', 'US', 'GB']),
            'is_vpn'          => $this->faker->boolean(5), // 5% chance VPN
            'is_flagged'      => false,
            'flag_reason'     => null,
        ];
    }

    /** Force a specific date */
    public function forDate(string $date): static
    {
        return $this->state(fn() => [
            'voted_at' => $date,
        ]);
    }

    /** Mark as today's vote */
    public function today(): static
    {
        return $this->state(fn() => [
            'voted_at' => now()->toDateString(),
        ]);
    }

    /** Mark as flagged */
    public function flagged(string $reason = 'velocity_burst'): static
    {
        return $this->state(fn() => [
            'is_flagged'  => true,
            'flag_reason' => $reason,
        ]);
    }

    /** Simulate a QR scan vote (physical visit) */
    public function fromQr(): static
    {
        return $this->state(fn() => [
            'vote_source' => 'qr',
        ]);
    }
}