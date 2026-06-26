<?php

namespace App\Notifications\User\Contributor;

use App\Models\Contributor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QualityScoreDroppedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Contributor $contributor, public float $score)
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
            ->subject('Your quality score has dropped')
            ->greeting("Hey {$notifiable->name},")
            ->line("Your quality score in **{$this->contributor->city->name}** has dropped to **{$this->score}**.")
            ->line('Rejected submissions affect your score. Focus on accurate and complete submissions to recover it.')
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
