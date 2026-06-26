<?php

return [

    'points' => [
        // Submissions
        'submission_created'  => 5,    // on submit (pending review)
        'submission_approved' => 20,   // bonus when admin approves
        'submission_rejected' => -5,   // penalty for bad submission

        // Claims
        'claim_created'       => 10,   // on claim request
        'claim_approved'      => 30,   // bonus when claim verified
        'claim_rejected'      => -10,  // penalty for false claim

        // Quality bonus (manually awarded by admin)
        'quality_bonus'       => 15,

        // Level up reward
        'level_bonus'         => 50,
    ],

    'levels' => [
        1 => ['name' => 'Scout',      'points_required' => 0,    'quality_score_required' => 0.0],
        2 => ['name' => 'Explorer',   'points_required' => 100,  'quality_score_required' => 0.5],
        3 => ['name' => 'Ranger',     'points_required' => 300,  'quality_score_required' => 0.6],
        4 => ['name' => 'Pathfinder', 'points_required' => 700,  'quality_score_required' => 0.7],
        5 => ['name' => 'Elite',      'points_required' => 1500, 'quality_score_required' => 0.8],
    ],

];