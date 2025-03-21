<?php

namespace App\Mail;

use App\Services\PdfGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;  // We will store the validated data here
    private $pdfPath;

    /**
     * Create a new message instance.
     */
    /**
     * Create a new message instance.
     */
    public function __construct(array $data, string $pdfPath) // expecting $data as a parameter
    {
        $this->data = $data; // Assign it to the class property
        $this->pdfPath = $pdfPath; // Store the PDF path as a class property
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Lead Based Paint Inspection',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'InvoiceMail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
//     public function attachments(): array
// {
//     return [
//         new \Illuminate\Mail\Mailables\Attachment(
//             path: $this->pdfPath,
//             filename: 'invoice.pdf',
//         ),
//     ];
// }

    /**
     * Build the message.
     */
    // Example Mailable class
    public function build()
    {
        // Check if the PDF file exists before attaching
        if (!file_exists($this->pdfPath)) {
            throw new \Exception("The attachment file does not exist at path: {$this->pdfPath}");
        }
        
        return $this->from(env('MAIL_FROM_ADDRESS')) // Sender's email address
                    ->replyTo(env('ADMIN_EMAIL')) // Set the reply-to email from the .env file
                    ->with(['data' => $this->data]) // Pass additional data to the email view
                    ->attach($this->pdfPath, [ 
                        'as' => basename($this->pdfPath), // Attach with the unique name
                        'mime' => 'application/pdf',
                    ])
                    ->markdown('InvoiceMail'); // Reference to the Markdown email view
    }

}
