<?php

namespace App\Mail;

use App\Services\ReportGenerator; // PDF Service
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AppSetting;

class XRFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $xrfData;
    public $pdfPath;
    public $settings;
    public $companyEmail;
    public $companyPhone;

    /**
     * Create a new message instance.
     */
    public function __construct($record, $pdfPath, $settings, $companyEmail, $companyPhone)
    {
        $this->record = $record;
        $this->pdfPath = $pdfPath;
        $this->settings = $settings;
        $this->companyEmail = $companyEmail;
        $this->companyPhone = $companyPhone;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lead-Based Paint Evaluation Report Inspection',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'XRFMail',
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
            return $this->markdown('XRFMail')
                        ->subject('XRF Report: Lead Based Paint Inspection')
                        ->with([
                            'data' => $data,
                            'settings' => $this->settings,
                            'companyEmail' => $this->companyEmail,
                            'companyPhone' => $this->companyPhone, 
                        ]);
        }

        return $this->from($this->companyEmail)
                    ->replyTo($this->companyEmail)
                    ->subject('Lead Based Paint Inspection Report')
                    ->markdown('XRFMail')
                    ->with([
                        'data' => $data,
                        'settings' => $this->settings,
                        'companyEmail' => $this->companyEmail,
                        'companyPhone' => $this->companyPhone,
                    ])
                    ->attach($pdfFullPath, [
                        'as' => "Xrf_Report_{$this->record->id}.pdf",
                        'mime' => 'application/pdf',
                    ]);
    }
} 