<?php

namespace App\Jobs;

use App\Models\City;
use App\Services\PulseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateCityPulseJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(PulseService $pulse): void
    {
        City::active()->each(function ($city) use ($pulse) {
            $score = $pulse->getPulseScore($city->id);

            // Only emit if score is notable (above 40)
            if ($score < 40) return;

            $pulse->createEvent(
                $city->id,
                'city_pulse',
                [
                    'pulse_score' => $score,
                    'city_name'   => $city->name,
                    'label'       => match(true) {
                        $score >= 80 => 'On fire 🔥',
                        $score >= 60 => 'Very active',
                        $score >= 40 => 'Picking up',
                        default      => 'Quiet',
                    },
                ]
            );
        });
    }
}