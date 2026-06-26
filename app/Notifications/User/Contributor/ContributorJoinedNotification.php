<?php

namespace App\Notifications\User\Contributor;

use App\Models\Contributor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributorJoinedNotification extends Notification implements ShouldQueue
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
            ->subject('Community Application Received')
            ->greeting("Hello {$notifiable->name},")
            ->line('Thank you for applying to become a '.config('app.name').' Community Contributor.')
            ->line("We have received your application for contributor access for {$this->contributor->city->name}.")
            ->line('Our team will review your application and notify you once a decision has been made.')
            ->line('As a contributor, you can help discover and add restaurants from your local area, helping great businesses get the recognition they deserve.')
            ->action('View Communities', route('settings.community.index'))
            ->line('Thank you for helping build a better '.config('app.name').'.');
    }
}
