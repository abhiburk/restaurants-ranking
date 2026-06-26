<?php

namespace App\Notifications\User\Contributor;

use App\Enums\ContributorAction;
use App\Models\City;
use App\Models\Contributor;
use App\Models\ContributorLevel;
use App\Models\ContributorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeveledUpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Contributor $contributor,public ContributorLevel $contributorLevel)
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
            ->subject("You reached {$this->contributorLevel->name}!")
            ->greeting("Congrats {$notifiable->name}!")
            ->line("You have leveled up to **{$this->contributorLevel->name}** in {$this->contributor->city->name}.")
            ->line('Keep contributing to unlock the next level.')
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
