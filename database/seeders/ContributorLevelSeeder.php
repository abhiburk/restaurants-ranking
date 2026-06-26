<?php

namespace Database\Seeders;

use App\Models\ContributorLevel;
use Illuminate\Database\Seeder;

class ContributorLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'level'                    => 1,
                'name'                     => 'Scout',
                'slug'                     => 'scout',
                'icon'                     => '🔍',
                'points_required'          => 0,
                'quality_score_required'   => 0,
                'monthly_submission_limit' => 5,
                'can_peer_review'          => false,
                'submission_auto_approve'  => false,
                'perks'                    => ['community_badge'],
            ],
            [
                'level'                    => 2,
                'name'                     => 'Local Guide',
                'slug'                     => 'local-guide',
                'icon'                     => '🗺️',
                'points_required'          => 100,
                'quality_score_required'   => 60,
                'monthly_submission_limit' => 15,
                'can_peer_review'          => false,
                'submission_auto_approve'  => false,
                'perks'                    => ['community_badge', 'early_access'],
            ],
            [
                'level'                    => 3,
                'name'                     => 'Area Captain',
                'slug'                     => 'area-captain',
                'icon'                     => '📍',
                'points_required'          => 300,
                'quality_score_required'   => 70,
                'monthly_submission_limit' => 30,
                'can_peer_review'          => false,
                'submission_auto_approve'  => false,
                'perks'                    => ['community_badge', 'early_access', 'city_insights'],
            ],
            [
                'level'                    => 4,
                'name'                     => 'City Champion',
                'slug'                     => 'city-champion',
                'icon'                     => '🏆',
                'points_required'          => 750,
                'quality_score_required'   => 80,
                'monthly_submission_limit' => 60,
                'can_peer_review'          => true,
                'submission_auto_approve'  => false,
                'perks'                    => ['community_badge', 'early_access', 'city_insights', 'peer_reviewer', 'beta_features'],
            ],
            [
                'level'                    => 5,
                'name'                     => 'Pioneer',
                'slug'                     => 'pioneer',
                'icon'                     => '⭐',
                'points_required'          => 2000,
                'quality_score_required'   => 90,
                'monthly_submission_limit' => 999,
                'can_peer_review'          => true,
                'submission_auto_approve'  => true,
                'perks'                    => ['community_badge', 'early_access', 'city_insights', 'peer_reviewer', 'beta_features', 'pioneer_forever'],
            ],
        ];

        foreach ($levels as $level) {
            ContributorLevel::create($level);
        }
    }
}   
