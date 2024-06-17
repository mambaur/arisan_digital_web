<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use williamcruzme\FCM\Messages\FcmMessage;

class ChargeNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $title;
    private $description;
    private $resource;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $description, $resource = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->resource = $resource;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'fcm'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'type' => 'charge',
            'resource' => $this->resource,
        ];
    }

    /**
     * Get the Firebase Message representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Williamcruzme\Fcm\Messages\FcmMessage
     */
    public function toFcm($notifiable)
    {
        return (new FcmMessage)
            ->notification([
                'title' => $this->title,
                'body' => $this->description,
            ])->data($this->resource);
    }
}
