<?php

namespace App\Mail;

use App\Services\ReportGenerator; // PDF Service
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisualMail extends Mailable
{
    use Queueable, SerializesModels;

    public $record;
    public $signatureUrl;
    public $xrfData;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct($record, $pdfPath, $signatureUrl, $xrfData)
    {
        $this->record = $record;
        $this->pdfPath = $pdfPath;
        $this->signatureUrl = $signatureUrl;
        $this->xrfData = $xrfData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lead-Based Paint Evaluation Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'VisualMail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    // public function attachments(): array
    // {
    //     return [];
    // }

    // Example Mailable class
    public function build()
    {
        $pdfFullPath = storage_path("app/public/{$this->pdfPath}");

        $data = [
            'name' => $this->record->property_owner_name ?? 'Customer',
            'InspectionType' => $this->record->inspection_type ?? 'inspection',
            'address' => $this->record->address ?? 'Unknown Address',
            'unit_number' => $this->record->unit_number ?? '',
            'municipality' => $this->record->municipality ?? 'Unknown Municipality',
            'city' => $this->record->city ?? 'Unknown City',
        ];

        if (!file_exists($pdfFullPath)) {
            \Log::error("PDF Attachment Missing: {$pdfFullPath}");
            return $this->markdown('VisualMail')
                        ->subject('Visual Report: Lead Based Paint Inspection')
                        ->with('data', $data);
        }

        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->replyTo(env('ADMIN_EMAIL'))
                    ->subject('Lead Based Paint Inspection Report')
                    ->markdown('VisualMail')
                    ->with('data', $data)
                    ->attach($pdfFullPath, [
                        'as' => "Visual_Report_{$this->record->id}.pdf",
                        'mime' => 'application/pdf',
                    ]);
    }
}
