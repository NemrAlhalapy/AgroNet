<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateComment extends Notification
{
    use Queueable;

    private $comment_id;
    private $usercomment;
    
    public function __construct($comment_id,$usercomment)
    {
        $this->comment_id=$comment_id;
        $this->usercomment=$usercomment;
    }

   
    public function via(object $notifiable): array
    {
        return ['database'];
    }

   
   
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id'=>$this->comment_id,
            'usercomment'=>$this->usercomment,
        ];
    }
}
