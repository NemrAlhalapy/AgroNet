<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateProduct extends Notification
{
    use Queueable;

    private $product_id;
    private $product_name;
    private $company_name;
    public function __construct($product_id,$product_name,$company_name)
    {
        $this->product_id=$product_id;
        $this->product_name=$product_name;
        $this->company_name=$company_name;
    }

    
    public function via(object $notifiable): array
    {
        return ['database'];
    }

   
    public function toArray(object $notifiable): array
    {
        return [
            'product_id'=>$this->product_id,
            'product_name'=>$this->product_name,
            'company_name'=>$this->company_name,    
        ];
    }
}
