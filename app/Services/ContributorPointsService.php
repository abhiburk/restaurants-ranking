<?php

namespace App\Services;

use App\Enums\ContributorAction;
use App\Models\Contributor;
use App\Models\ContributorLevel;
use App\Models\ContributorLog;
use App\Notifications\User\Contributor\LeveledUpNotification;
use App\Notifications\User\Contributor\LevelUpBlockedNotification;
use App\Notifications\User\Contributor\PointsAwardedNotification;
use App\Notifications\User\Contributor\QualityBonusAwardedNotification;
use App\Notifications\User\Contributor\QualityScoreDroppedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContributorPointsService
{
    /**
     * Core method — award or penalize points for any action.
     *
     * @param  Model|null  $loggable  The related model (Submission, Claim…)
     */
    public function award(Contributor $contributor, ContributorAction $action, ?Model $loggable = null, ?string $note = null): void
    {
        $points = config("contributor.points.{$action->value}");

        if ($points === null) {
            Log::warning("ContributorPointsService: No points config for action [{$action->value}].");
            return;
        }

        DB::transaction(function () use ($contributor, $action, $points, $loggable, $note) {
            $log = ContributorLog::create([
                'contributor_id' => $contributor->id,
                'points'         => $points,
                'action'         => $action->value,
                'loggable_id'    => $loggable?->getKey(),
                'loggable_type'  => $loggable?->getMorphClass(),
                'note'           => $note,
            ]);

            $contributor->increment('points', $points);

            $this->checkLevelUp($contributor->fresh());

            $contributor->user->notify(new PointsAwardedNotification($log, $action));
        });
    }

    // ── Submission Actions ────────────────────────────────────────────────────

    /**
     * Contributor submitted a new restaurant — pending review.
     */
    public function onSubmissionCreated(Contributor $contributor, Model $submission): void
    {
        $this->award($contributor, ContributorAction::SubmissionCreated, $submission);
    }

    /**
     * Admin approved the submission — award bonus points.
     */
    public function onSubmissionApproved(Contributor $contributor, Model $submission): void
    {
        $this->award($contributor, ContributorAction::SubmissionApproved, $submission);
        $this->updateQualityScore($contributor->fresh());
    }

    /**
     * Admin rejected the submission — apply penalty.
     */
    public function onSubmissionRejected(Contributor $contributor, Model $submission, ?string $reason = null): void
    {
        $this->award($contributor, ContributorAction::SubmissionRejected, $submission, $reason);
        $this->updateQualityScore($contributor->fresh());
    }

    // ── Claim Actions ─────────────────────────────────────────────────────────

    /**
     * Contributor submitted a restaurant ownership claim.
     */
    public function onClaimCreated(Contributor $contributor, Model $claim): void
    {
        $this->award($contributor, ContributorAction::ClaimCreated, $claim);
    }

    /**
     * Admin verified and approved the claim.
     */
    public function onClaimApproved(Contributor $contributor, Model $claim): void
    {
        $this->award($contributor, ContributorAction::ClaimApproved, $claim);
        $this->updateQualityScore($contributor->fresh());
    }

    /**
     * Admin rejected the claim — apply penalty.
     */
    public function onClaimRejected(Contributor $contributor, Model $claim, ?string $reason = null): void
    {
        $this->award($contributor, ContributorAction::ClaimRejected, $claim, $reason);
        $this->updateQualityScore($contributor->fresh());
    }

    // ── Admin Manual Award ────────────────────────────────────────────────────

    /**
     * Admin manually awards a quality bonus (e.g. exceptionally detailed submission).
     */
    public function awardQualityBonus(Contributor $contributor, ?Model $loggable = null, ?string $note = null): void
    {
        $this->award($contributor, ContributorAction::QualityBonus, $loggable, $note);

        $points = config('contributor.points.quality_bonus');
        $contributor->user->notify(new QualityBonusAwardedNotification($points, $note));
    }

    // ── Queries ───────────────────────────────────────────────────────────────

    /**
     * Paginated points history for a contributor.
     */
    public function getHistory(Contributor $contributor, int $perPage = 20)
    {
        return ContributorLog::where('contributor_id', $contributor->id)
            ->with('loggable')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Total positive points earned this month.
     */
    public function getMonthlyPoints(Contributor $contributor): int
    {
        return ContributorLog::where('contributor_id', $contributor->id)
            ->thisMonth()
            ->positive()
            ->sum('points');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    /**
     * Promote contributor if they qualify for the next level.
     */
    private function checkLevelUp(Contributor $contributor): void
    {
        // Step 1: check points only
        $nextLevel = ContributorLevel::where('level', '>', $contributor->contributor_level->level)
            ->where('points_required', '<=', $contributor->points)
            ->where('quality_score_required', '<=', $contributor->quality_score)
            ->orderBy('level')
            ->first();

        if (!$nextLevel) return;

        // Step 2: check quality score
        if ($contributor->quality_score < $nextLevel->quality_score_required) {
            $contributor->user->notify(new LevelUpBlockedNotification($contributor, $nextLevel));
            return;
        }

        // Step 3: qualify for level up
        $contributor->update(['level' => $nextLevel->level]);
        $contributor->user->notify(new LeveledUpNotification($contributor, $nextLevel));
        $this->award($contributor->fresh(), ContributorAction::LevelBonus, null, "Reached Level {$nextLevel->level}: {$nextLevel->name}");
    }

    /**
     * Recalculate and persist the contributor's quality score.
     * Call this after every approval or rejection.
     */
    private function updateQualityScore(Contributor $contributor): void
    {
        $approved = ContributorLog::where('contributor_id', $contributor->id)
            ->whereIn('action', [ContributorAction::SubmissionApproved->value, ContributorAction::ClaimApproved->value])
            ->count();

        $rejected = ContributorLog::where('contributor_id', $contributor->id)
            ->whereIn('action', [ContributorAction::SubmissionRejected->value, ContributorAction::ClaimRejected->value])
            ->count();

        $total = $approved + $rejected;

        $score = $total > 0 ? round($approved / $total, 2) : 0.00;

        $previousScore = $contributor->quality_score;

        $contributor->update(['quality_score' => $score]);

        if ($score < $previousScore && $score < 0.50) {
            $contributor->user->notify(new QualityScoreDroppedNotification($contributor, $score));
        }
    }

    /**
     * Get level progress info for a contributor in a specific city.
     */
    public function getLevelProgress(Contributor $contributor): array
    {
        $currentLevel = ContributorLevel::where('id', $contributor->contributor_level_id)->first();

        $nextLevel = ContributorLevel::where('points_required', '>', $currentLevel->points_required)
            ->orderBy('points_required')
            ->first();

        // Already at max level
        if (!$nextLevel) {
            return [
                'current_level'     => $currentLevel->name,
                'next_level'        => null,
                'points_current'    => $contributor->points,
                'points_required'   => $currentLevel->points_required,
                'points_remaining'  => 0,
                'percentage'        => 100,
                'is_max_level'      => true,

            ];
        }

        $pointsIntoCurrentLevel = $contributor->points - $currentLevel->points_required;
        $pointsNeededForNext    = $nextLevel->points_required - $currentLevel->points_required;
        $percentage             = max(0, round(($pointsIntoCurrentLevel / $pointsNeededForNext) * 100));

        return [
            'current_level'    => $currentLevel->name,
            'next_level'       => $nextLevel->name,
            'points_current'   => $contributor->points,
            'points_required'  => $nextLevel->points_required,
            'points_remaining' => $nextLevel->points_required - $contributor->points,
            'percentage'       => min($percentage, 100), // cap at 100 in edge cases
            'is_max_level'     => false,
            'quality_score_required' => $nextLevel->quality_score_required,
            'quality_score_met'      => $contributor->quality_score >= $nextLevel->quality_score_required,
        ];
    }
}
