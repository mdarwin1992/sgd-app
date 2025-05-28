<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DocumentLoan;

class DocumentLoanOverdue extends Notification implements ShouldQueue
{
    use Queueable;

    protected $documentLoan;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\DocumentLoan  $documentLoan
     * @return void
     */
    public function __construct(DocumentLoan $documentLoan)
    {
        $this->documentLoan = $documentLoan;
    }

    /**
     * The channels the notification should be sent on.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // You can add 'mail' or other channels
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The return date for document loan ' . $this->documentLoan->order_number . ' has passed.')
            ->action('View Loan', url('/loans/' . $this->documentLoan->id)) // Adjust the URL
            ->line('Please take necessary action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'document_loan_id' => $this->documentLoan->id,
            'order_number' => $this->documentLoan->order_number,
            'return_date' => $this->documentLoan->return_date,
            'message' => 'The return date for document loan ' . $this->documentLoan->order_number . ' has passed.',
        ];
    }
}
