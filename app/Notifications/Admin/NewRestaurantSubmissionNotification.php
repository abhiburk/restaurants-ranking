<?php

namespace App\Notifications\Admin;

use App\Models\RestaurantSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRestaurantSubmissionNotification extends Notification implements ShouldQueue
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
            ->subject('New Restaurant Submission')
            ->greeting('Hello Admin,')
            ->line('A new restaurant submission has been received.')
            ->line('**Restaurant:** ' . $this->submission->name)
            ->line('**City:** ' . $this->submission->city->name)
            ->line('**Submitted By:** ' . $this->submission->user->name)
            ->line('**Contributor Email:** ' . $this->submission->user->email)
            ->action(
                'Review Submission',
                route('filament.admin.resources.restaurant-submissions.index', [
                    'search' => $this->submission->name
                ])
            )
            ->line('Please review and take the appropriate action.');
    }
}
