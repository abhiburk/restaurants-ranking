<?php

namespace App\Notifications\User\RestaurantClaim;

use App\Models\RestaurantClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestaurantClaimApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Restaurant Claim Approved 🎉')
            ->greeting("Congratulations {$notifiable->name}!")
            ->line('Your claim request has been approved.')
            ->line("You are now the verified owner of **{$this->claim->restaurant->name}** on ".config('app.name'))
            ->line('You can now manage your restaurant profile and participate in future owner features as they become available.')
            ->action(
                'View Restaurant',
                route('filament.partner.resources.restaurants.index', [
                    'search' => $this->claim->restaurant->name
                ])
            )
            ->line('Thank you for being part of '.config('app.name'));
    }
}
