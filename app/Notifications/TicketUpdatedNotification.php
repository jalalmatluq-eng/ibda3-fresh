<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketUpdatedNotification extends Notification
{
    use Queueable;

    public $ticket;
    public $messageStr;

    public function __construct(Ticket $ticket, $messageStr)
    {
        $this->ticket = $ticket;
        $this->messageStr = $messageStr;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Internal notifications only
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => $this->messageStr,
            'url' => url('/tickets/' . $this->ticket->id),
        ];
    }
}
