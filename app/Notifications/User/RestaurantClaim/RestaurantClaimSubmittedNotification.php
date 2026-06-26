<?php

namespace App\Notifications\User\RestaurantClaim;

use App\Models\RestaurantClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestaurantClaimSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RestaurantClaim $claim,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Restaurant Claim Received')
            ->greeting("Hello {$notifiable->name},")
            ->line('Thank you for submitting your restaurant ownership claim.')
            ->line("We have received your request to claim **{$this->claim->restaurant->name}**.")
            ->line('Our team will review the information and supporting documents provided.')
            ->line('You will be notified once a decision has been made.')
            ->action(
                'View Restaurant',
                route('restaurants.show', $this->claim->restaurant)
            )
            ->line('Thank you for helping keep '.config('app.name').' accurate and up to date.');
    }
}
