<?php

namespace App\Notifications\Admin;

use App\Models\RestaurantClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRestaurantClaimNotification extends Notification implements ShouldQueue
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
            ->subject('New Restaurant Claim Request')
            ->greeting('Hello Admin,')
            ->line('A new restaurant ownership claim has been submitted.')
            ->line("**Restaurant:** {$this->claim->restaurant->name}")
            ->line("**City:** {$this->claim->city->name}")
            ->line("**Submitted By:** {$this->claim->user->name}")
            ->action(
                'Review Claim',
                route('filament.admin.resources.restaurant-claims.index')
            )
            ->line('Please review the submitted proof and take the appropriate action.');
    }
}
