<?php

namespace App\Mail;

use App\Models\AppSetting;
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
        $settings = AppSetting::first();

        // Log a warning if settings are not found
        if (!$settings) {
            \Log::warning('AppSetting not found when generating InvoiceMail.');
        }

        // Check if the PDF file exists before attaching
        if (!file_exists($this->pdfPath)) {
            \Log::error("The attachment file does not exist at path: {$this->pdfPath}");
            return $this->markdown('InvoiceMail')
                        ->with([
                            'data' => $this->data,
                            'settings' => $settings,
                        ]);
        }

        return $this->from($settings->company_email ?? env('MAIL_FROM_ADDRESS', 'default@example.com'))
                    ->replyTo($settings->company_email ?? env('ADMIN_EMAIL', 'admin@example.com'))
                    ->with([
                        'data' => $this->data,
                        'settings' => $settings,
                    ])
                    ->attach($this->pdfPath, [
                        'as' => basename($this->pdfPath),
                        'mime' => 'application/pdf',
                    ])
                    ->markdown('InvoiceMail');
    }
}