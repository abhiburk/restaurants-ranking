<?php

namespace App\Notifications\User\RestaurantSubmission;

use App\Models\RestaurantSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestaurantSubmissionApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Restaurant Submission Approved 🎉')
            ->greeting('Congratulations '.$notifiable->name.'!')
            ->line('Your restaurant submission has been approved and is now live on '.config('app.name').'.')
            ->line("**Restaurant:** {$this->submission->name}")
            ->line("**City:** {$this->submission->city->name}")
            ->line('Your contribution is helping local restaurants gain visibility and recognition.')
            ->action(
                'View Restaurant',
                route('restaurants.show', [$this->submission->city, $this->submission->restaurant])
            )
            ->line('Thank you for supporting your local food community.');
    }
}
