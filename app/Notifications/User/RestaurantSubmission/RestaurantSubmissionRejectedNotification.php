<?php

namespace App\Notifications\User\RestaurantSubmission;

use App\Models\RestaurantSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestaurantSubmissionRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RestaurantSubmission $submission,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Restaurant Submission Update')
            ->greeting("Hello {$notifiable->name},")
            ->line('We reviewed your restaurant submission but were unable to approve it at this time.')
            ->line("**Restaurant:** {$this->submission->name}")
            ->line("**City:** {$this->submission->city->name}")
            ->when(
                filled($this->submission->reason),
                fn(MailMessage $mail) => $mail->line(
                    '**Reason:** ' . $this->submission->reason
                )
            )
            ->line('You may review the information and submit again in the future.')
            ->action('View Communities', route('settings.community.index'))
            ->line('Thank you for helping improve ' . config('app.name') . '.');
    }
}
