<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreatePost extends Notification
{
    use Queueable;

    private $post_id;
    private $usercreate;

    public function __construct($post_id,$usercreate)
    {
        $this->post_id=$post_id;
        $this->usercreate=$usercreate;
    }

    
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    
    
    public function toArray(object $notifiable): array
    {
        return [
            'post_id'=>$this->post_id,
            'usercreate'=>$this->usercreate,
        ];
    }
}
