<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class replyingConsultation extends Notification
{
    use Queueable;
    private $consultation_id;
    private $engineername;

    
    public function __construct($consultation_id,$engineername)
    {
        $this->consultation_id=$consultation_id;
        $this->engineername=$engineername;
    }

   
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    
    public function toArray(object $notifiable): array
    {
        return [
            'consultation_id'=>$this->consultation_id,
            'engineername'=>$this->engineername
        ];
    }
}
