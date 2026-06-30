<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VoteSeeder extends Seeder
{
    public $daysOfData = 7;

    public function run(): void
    {
        $restaurants = Restaurant::all();

        if ($restaurants->isEmpty()) {
            $this->command->warn('No restaurants found. Run RestaurantSeeder first.');

            return;
        }

        $this->command->info("Seeding votes for {$restaurants->count()} restaurants over ".$this->daysOfData.' days...');
        $bar = $this->command->getOutput()->createProgressBar($restaurants->count());

        foreach ($restaurants as $index => $restaurant) {
            $this->seedForRestaurant($restaurant, $index);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Done. Total votes: '.Vote::count());
    }

    private function seedForRestaurant(Restaurant $restaurant, int $index): void
    {
        $personality = match ($index % 5) {
            0 => 'consistent_leader',  // HIGH streak (wins most days)
            1 => 'rising_star',        // trending (velocity accelerating)
            2 => 'weekend_warrior',    // streak on weekends only
            3 => 'volatile',           // random highs — occasional trending
            4 => 'underdog',           // low volume, no streak/trending
        };

        $today = now();

        for ($daysAgo = $this->daysOfData; $daysAgo >= 0; $daysAgo--) {
            $date = $today->copy()->subDays($daysAgo)->toDateString();
            $count = $this->votesForDay($personality, $daysAgo, $date);

            $this->insertVotesForDate($restaurant, $date, $count);
        }
    }

    private function votesForDay(string $personality, int $daysAgo, string $date): int
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $isWeekend = in_array($dayOfWeek, [0, 5, 6]);
        $isToday = $daysAgo === 0;

        return match ($personality) {

            // Consistently high — wins almost every day → long streak
            // Last 2 hours of today get a burst to trigger isTrendingToday()
            'consistent_leader' => $isToday
            ? rand(2800, 3400)   // noticeably higher than any other personality
            : rand(2200, 2800) + ($isWeekend ? rand(200, 400) : 0),

            // Votes accelerate strongly over the last 30 days
            // Recent days are significantly higher than earlier days
            // → triggers isTrendingToday() because last 2hrs >> prev 2hrs
            'rising_star' => $isToday
    ? rand(1400, 1900)
    : match (true) {
        $daysAgo >= 20 => rand(100, 300),
        $daysAgo >= 10 => rand(400, 800),
        $daysAgo >= 3 => rand(900, 1400),
        default => rand(1200, 1600),
    },

            // Spikes hard on weekends → weekend streak possible
            'weekend_warrior' => $isToday
    ? ($isWeekend ? rand(1600, 2000) : rand(300, 600))
    : ($isWeekend ? rand(1400, 1800) : rand(150, 400)),

            // Random spikes — some days trending, never a long streak
            'volatile' => $isToday
    ? rand(400, 1400)   // capped lower so it never beats consistent_leader
    : (rand(1, 5) === 1 ? rand(1200, 1800) : rand(100, 500)),

            // Always low — never trending, never streaking
            'underdog' => $isToday
    ? rand(100, 450)
    : rand(50, 300),

            default => rand(200, 800),
        };
    }

    private function insertVotesForDate(Restaurant $restaurant, string $date, int $count): void
    {
        $isToday = $date === now()->toDateString();
        $personality = $restaurant->_seeder_personality = fake()->randomElement([
            'consistent_leader',
            'consistent_leader', // weighted 2:1 toward streak
            'rising_star',
        ]);
        $shouldBurst = $isToday && in_array($personality, ['consistent_leader', 'rising_star']);

        $chunks = array_chunk(range(1, $count), 500);

        foreach ($chunks as $chunk) {
            $rows = [];

            foreach ($chunk as $i) {
                $ip = fake()->ipv4();
                $ua = fake()->userAgent();
                $source = fake()->randomElement(['qr_scan', 'qr_scan', 'qr_scan', 'direct_link', 'leaderboard']);
                $country = fake()->randomElement(['IN', 'IN', 'IN', 'IN', 'US']);
                $isVpn = fake()->boolean(5);
                $isFlagged = $isVpn && fake()->boolean(60);

                // Burst logic: push 60% of today's votes into the last 2 hours
                if ($shouldBurst) {
                    $createdAt = fake()->boolean(60)
                        // Last 2 hours — triggers trending
                        ? now()->subMinutes(rand(0, 120))
                        // Earlier today
                        : Carbon::parse($date)
                            ->addSeconds(rand(0, now()->secondsSinceMidnight() - 7200));
                } elseif ($isToday) {
                    // Normal today distribution — spread across the day so far
                    $createdAt = Carbon::parse($date)
                        ->addSeconds(rand(0, now()->secondsSinceMidnight()));
                } else {
                    // Past day — any time during that day
                    $createdAt = Carbon::parse($date)->addSeconds(rand(0, 86399));
                }

                $rows[] = [
                    'id' => Str::uuid()->toString(),
                    'restaurant_id' => $restaurant->id,
                    'user_id' => null,
                    'city_id' => $restaurant->city_id,
                    'voted_at' => $date,
                    'visitor_id' => Str::uuid()->toString(),
                    'ip_address' => $ip,
                    'ip_hash' => hash('sha256', $ip.$date),
                    'user_agent_hash' => hash('sha256', $ua.$date),
                    'vote_source' => $source,
                    'country_code' => $country,
                    'is_vpn' => $isVpn,
                    'is_flagged' => $isFlagged,
                    'flag_reason' => $isFlagged ? 'vpn_detected' : null,
                    // 'deleted_at' => $isFlagged ? now() : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            Vote::insert($rows);
        }
    }
}
