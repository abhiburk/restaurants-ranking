<?php

namespace App\Notifications\User\Contributor;

use App\Models\Contributor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributorRequestApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Contributor $contributor,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Welcome to the {$this->contributor->city->name} Community")
            ->greeting("Congratulations {$notifiable->name}! 🎉,")
            ->line("Your community application for {$this->contributor->city->name} has been approved.")
            ->line('You can now start contributing by adding restaurants from your area and helping '.config('app.name').' grow.')
            ->line('Every approved restaurant strengthens your contributor profile and helps local businesses get discovered.')
            ->action('View Communities', route('settings.community.index'))
            ->line('Welcome to the '.config('app.name').' contributor community.');
    }
}
