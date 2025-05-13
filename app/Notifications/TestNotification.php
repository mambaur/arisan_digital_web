<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TestNotification extends Notification
{
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
        return ['database',  FcmChannel::class];
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
     */
    public function toFcm($notifiable)
    {   
        return (new FcmMessage(notification: new FcmNotification(
            title: $this->title,
            body: $this->description,
        )))
            ->data(['data' => $this->resource, 'type' => 'test']);
    }
}
