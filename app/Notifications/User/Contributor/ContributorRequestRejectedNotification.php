<?php

namespace App\Notifications\User\Contributor;

use App\Models\Contributor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributorRequestRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Community Application Update')
            ->greeting("Hello {$notifiable->name},")
            ->line('Thank you for your interest in becoming a ' . config('app.name') . ' contributor.')
            ->line("After reviewing your application for the **{$this->contributor->city->name}** community, we are unable to approve it at this time.")
            ->line('This does not prevent you from applying again in the future.')
            ->line('If additional information is required, our team may reach out separately.')
            ->when(
                filled($this->contributor->reason),
                fn(MailMessage $mail) => $mail->line(
                    '**Reason:** ' . $this->contributor->reason
                )
            )
            ->action('View Communities', route('settings.community.index'))
            ->line('Thank you for supporting local restaurants.');
    }
}
