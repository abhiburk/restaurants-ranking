<?php

namespace App\Enums;

enum ContributorAction: string
{
    // Submissions
    case SubmissionCreated  = 'submission_created';
    case SubmissionApproved = 'submission_approved';
    case SubmissionRejected = 'submission_rejected';

    // Claims
    case ClaimCreated       = 'claim_created';
    case ClaimApproved      = 'claim_approved';
    case ClaimRejected      = 'claim_rejected';

    // Quality bonuses (admin-triggered)
    case QualityBonus       = 'quality_bonus';

    // Levels
    case LevelBonus         = 'level_bonus';

    public function label()
    {
        return match ($this) {
            self::SubmissionCreated  => 'Submission Created',
            self::SubmissionApproved => 'Submission Approved',
            self::SubmissionRejected => 'Submission Rejected',
            self::ClaimCreated       => 'Claim Created',
            self::ClaimApproved      => 'Claim Approved',
            self::ClaimRejected      => 'Claim Rejected',
            self::QualityBonus       => 'Quality Bonus',
            self::LevelBonus         => 'Level Bonus',
        };
    }
}