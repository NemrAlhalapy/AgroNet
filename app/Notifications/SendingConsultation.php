<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendingConsultation extends Notification
{
    use Queueable;

    private $consultation_id;
    private $farmername;
    public function __construct($consultation_id,$farmername)
    {
        $this->consultation_id=$consultation_id;
        $this->farmername=$farmername;
    }

   
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    
    public function toArray(object $notifiable): array
    {
        return [
            'consultation_id'=>$this->consultation_id,
            'farmername'=>$this->farmername
        ];
    }
}
