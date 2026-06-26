<?php

namespace App\Notifications\User\RestaurantClaim;

use App\Models\RestaurantClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestaurantClaimRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Restaurant Claim Update')
            ->greeting("Hello {$notifiable->name},")
            ->line('We have reviewed your restaurant ownership claim.')
            ->line("Unfortunately, we were unable to verify ownership of **{$this->claim->restaurant->name}** at this time.")
            ->when(
                filled($this->claim->reason),
                fn (MailMessage $mail) => $mail->line(
                    '**Reason:** '.$this->claim->reason
                )
            )
            ->line('You may submit a new claim with additional supporting documents if necessary.')
            ->line('Thank you for your understanding.');
    }
}
