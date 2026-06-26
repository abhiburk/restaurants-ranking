<?php

namespace App\Notifications\User\Contributor;

use App\Enums\ContributorAction;
use App\Models\ContributorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PointsAwardedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ContributorLog $contributorLog, public ContributorAction $action)
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
            ->subject("You just earned {$this->contributorLog->points} points!")
            ->greeting("Hey {$notifiable->name},!")
            ->line("You just earned **{$this->contributorLog->points} points** for {$this->action->label()}.")
            ->line("Your total points are now **{$notifiable->contributor->points}**.")
            ->action('View Activity', route('settings.leaderboard.show', $this->contributorLog->contributor));
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
