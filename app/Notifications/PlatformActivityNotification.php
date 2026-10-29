<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlatformActivityNotification extends Notification
{
    use Queueable;

    /** @param array{event:string,title:string,message:string,icon:string,url:string} $payload */
    public function __construct(private readonly array $payload)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array{event:string,title:string,message:string,icon:string,url:string} */
    public function toDatabase(object $notifiable): array
    {
        return $this->payload;
    }
}