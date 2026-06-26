<?php

namespace App\Notifications\User\Contributor;

use App\Enums\ContributorAction;
use App\Models\Contributor;
use App\Models\ContributorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QualityBonusAwardedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Contributor $contributor, public float $points, public ?string $note)
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
            ->subject('Quality bonus awarded!')
            ->greeting("Hey {$notifiable->name},")
            ->line("An admin has awarded you a **quality bonus of {$this->points} points**.")
            ->line($this->note ?? 'Keep up the great work!')
            ->action('View Activity', route('settings.leaderboard.show', $this->contributor));
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
