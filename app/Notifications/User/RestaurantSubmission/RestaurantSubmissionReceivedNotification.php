<?php

namespace App\Notifications\User\RestaurantSubmission;

use App\Models\RestaurantSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestaurantSubmissionReceivedNotification extends Notification implements ShouldQueue
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
            ->subject('Restaurant Submission Received')
            ->greeting("Hello {$notifiable->name},")
            ->line("Thank you for contributing to " . config('app.name'))
            ->line('We have received your restaurant submission and it is now awaiting review.')
            ->line("**Restaurant:** {$this->submission->name}")
            ->line("**City:** {$this->submission->city->name}")
            ->line('Our team will review the information and notify you once a decision has been made.')
            ->action('View My Submissions', route('filament.contributor.resources.restaurant-submissions.index'))
            ->line('Thank you for helping discover great restaurants.');
    }
}
