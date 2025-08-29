<?php

namespace App\Jobs;

use App\Mail\ParentMails;
use App\Models\SchoolParentMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Mail;

class SendMessagesJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public SchoolParentMessage $message;

    public function __construct(SendMessagesJob $message)
    {
        $this->message = $message->fresh('parentDeliveries.parent'); // ensure relations are loaded
    }

    public function handle(): void
    {
      
        foreach ($this->message->parentDeliveries as $delivery) {

          $parent=$delivery->parent;
          $title=$this->message->subject ?? 'New Message';
          $message=$this->message->content ?? 'New duplicationhub Message';

            if (in_array('email', $this->message->types)) {
                $delivery->delivered_via_email = true;
                $delivery->email_delivery_time = now();
                  
            }


            if (in_array('push', $this->message->types)) {
              
                 sendFirebaseNotification('parent', $parent->user->id, $message, $title);                
            }


            $delivery->response_logs = $logs;
            $delivery->save();
        }
    }
}
