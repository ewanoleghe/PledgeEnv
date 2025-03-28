<?php

namespace App\Services;

use PDF; // Import the PDF facade from the package
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PdfGenerator
{
    private $PDF;

    // public function __construct(PdfGenerator $pdfGenerator) // Dependency Injection
    // {
    //     $this->pdfGenerator = $pdfGenerator;
    // }

    public function generatePdf(array $data)
    {
        // Check the selected payment method and return the appropriate view name
        if ($data['selectedPaymentMethod'] === 'MANUAL') {
            return PDF::loadView('pdf.invoice', compact('data'))->stream();
        } elseif ($data['selectedPaymentMethod'] === 'ELECTRONICS') {
            return PDF::loadView('pdf.receipt', compact('data'))->stream();
        } else {
            // Handle unknown payment method
            throw new \InvalidArgumentException('Unknown payment method');
        }
    }

    public function saveInvoice(array $data, $fileName)
    {
        $view = $this->getPdfView($data);

        // Ensure the filename doesn’t already have .pdf
        $fileName = rtrim($fileName, '.pdf'); // Remove .pdf if present
        $filePath = $this->getFilePath($fileName, $view);

        // Create the directory if it doesn’t exist
        $directory = dirname($filePath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $pdf = PDF::loadView($view, compact('data'));
        $pdf->save($filePath);

        return $filePath;
    }

    private function getPdfView(array $data)
    {
        // Based on the view name, determine the appropriate file path to save the PDF
        if ($data['payment_status'] === 'pending') {
            return 'pdf.invoice';
        } elseif ($data['payment_status'] === 'succeeded') {
            return 'pdf.receipt';
        } else {
            // Handle unknown payment method
            throw new \InvalidArgumentException('Unknown payment method');
        }
    }

    private function getFilePath($fileName, $viewName)
    {
        if ($viewName === 'pdf.invoice') {
            return storage_path("app/public/invoices/{$fileName}.pdf");
        } elseif ($viewName === 'pdf.receipt') {
            return storage_path("app/public/receipts/{$fileName}.pdf");
        } else {
            // Handle unknown view name
            throw new \InvalidArgumentException('Unknown view name');
        }
    }

}