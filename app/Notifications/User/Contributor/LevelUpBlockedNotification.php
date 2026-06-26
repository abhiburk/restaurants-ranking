<?php

namespace App\Notifications\User\Contributor;

use App\Models\Contributor;
use App\Models\ContributorLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LevelUpBlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Contributor $contributor, public ContributorLevel $contributorNextLevel)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your level up is being blocked')
            ->greeting("Hey {$notifiable->name},")
            ->line("You have enough points to reach **{$this->contributorNextLevel->name}** but your quality score is holding you back.")
            ->line("Required score: **{$this->contributorNextLevel->quality_score_required}** · Your score: **{$this->contributor->quality_score}**")
            ->line('Improve your score by submitting accurate and detailed restaurants.')
            ->action('View Leaderboard', route('settings.leaderboard.show', $this->contributor));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
