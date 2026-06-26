<?php

namespace App\Notifications\Admin;

use App\Models\Contributor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributorJoinedAdminNotification extends Notification implements ShouldQueue
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
            ->subject('New Community Contributor Application')
            ->greeting('Hello Admin,')
            ->line('A new contributor application has been submitted.')
            ->line("**Name:** {$this->contributor->user->name}")
            ->line("**Email:** {$this->contributor->user->email}")
            ->line("**City:** {$this->contributor->city->name}")
            ->line('Please review the application and take the appropriate action.')
            ->action(
                'View Contributors',
                route('filament.admin.resources.contributor-applications.index', [
                    'search' => $this->contributor->user->name
                ])
            );
    }
}
