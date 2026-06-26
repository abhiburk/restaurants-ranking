<?php

namespace App\Notifications;

use App\Models\City;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CityAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public City $city
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🎉 {$this->city->name} is now live on ". config('app.name'))
            ->greeting('Good news!')
            ->line("{$this->city->name} is now available on ". config('app.name') . " and we wanted to let you know right away.")
            ->line('Discover top-ranked restaurants and vote for your favorites.')
            ->action(
                'Explore Restaurants',
                route('restaurants.index', $this->city->slug)
            )
            ->line('Thank you for joining the waitlist.');
    }
}