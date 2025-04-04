<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;
use App\Models\Record;
use App\Models\AppSetting;

class ReportGenerator
{
    public function generateReport($id)
    {
        $record = Record::findOrFail($id);
        $settings = AppSetting::first(); // Fetch settings
        $pdfFiles = [];
        $certificateFiles = [];
        
        if ($record->includeXrf) {
            $pdfFiles[] = $this->generatePdf('pdf.xrf', $record, "xrf_report_{$record->id}.pdf");
            $this->addIfValid($certificateFiles, storage_path("app/public/{$record->xrf_report}")); // Landscape
            $this->addIfValid($pdfFiles, storage_path("app/public/{$record->floor_plan}"));
            
            if ($record->xrf_pass_fail === 'pass') {
                $certificateFiles[] = $this->generatePdf('pdf.certificatefree', $record, "certificatefree_{$record->id}.pdf", 'landscape');
            }
        }
        
        if ($record->methodology === 'Visual Inspection' || (!$record->includeXrf && $record->methodology !== 'Dust Wipe Sampling') || ($record->includeXrf && $record->xrf_pass_fail === 'fail')) {
            $pdfFiles[] = $this->generatePdf('pdf.visual', $record, "visual_report_{$record->id}.pdf");
            $this->addIfValid($pdfFiles, storage_path("app/public/{$record->floor_plan}"));
            
            if ($record->pass_fail === 'pass') {
                $certificateFiles[] = $this->generatePdf('pdf.certificate', $record, "certificate_{$record->id}.pdf", 'landscape');
            }
        }
        
        if ($record->methodology === 'Dust Wipe Sampling' || (!$record->includeXrf && $record->methodology !== 'Visual Inspection') || ($record->includeXrf && $record->xrf_pass_fail === 'fail')) {
            $pdfFiles[] = $this->generatePdf('pdf.dust', $record, "dust_report_{$record->id}.pdf");
            $this->addIfValid($pdfFiles, storage_path("app/public/{$record->floor_plan}"));
            $this->addIfValid($pdfFiles, storage_path("app/public/{$record->lab_report}"));
            
            if ($record->pass_fail === 'pass') {
                $certificateFiles[] = $this->generatePdf('pdf.certificate', $record, "certificate_{$record->id}.pdf", 'landscape');
            }
        }
        
        $finalPdfPath = storage_path("app/private/final_reports/report_{$record->id}.pdf");
        
        if (!empty($pdfFiles) || !empty($certificateFiles)) {
            $this->mergePdfs($pdfFiles, $certificateFiles, $finalPdfPath);
            $this->cleanupTempFiles($pdfFiles);
            return response()->download($finalPdfPath)->deleteFileAfterSend(true);
        }
        
        return response()->json(['error' => 'No valid report conditions met.'], 400);
    }

    public function saveReport(array $data, $fileName)
    {
        if (!isset($data['record']) || empty($data['record'])) {
            \Log::error("PDF Generation Failed: Record data is missing.");
            return false;
        }

        $record = $data['record'];
        $signatureUrl = $data['signatureUrl'];
        
        if (is_array($record)) {
            $record = (object) $record;
        }

        $tempDir = storage_path('app/private/temp');
        $finalDir = storage_path('app/public/reports');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
            \Log::info("Created temp directory: $tempDir");
        }
        if (!file_exists($finalDir)) {
            mkdir($finalDir, 0755, true);
            \Log::info("Created reports directory: $finalDir");
        }

        try {
            $pdfFiles = [];        // Portrait PDFs
            $certificateFiles = []; // Landscape PDFs

            \Log::debug("Processing report for Record ID: {$record->id}", [
                'methodology' => $record->methodology,
                'includeXrf' => $record->includeXrf,
                'xrf_pass_fail' => $record->xrf_pass_fail,
                'pass_fail' => $record->pass_fail,
                'xrf_report' => $record->xrf_report,
                'floor_plan' => $record->floor_plan,
                'lab_report' => $record->lab_report,
            ]);

            // Condition 1: Visual Inspection, No XRF
            if ($record->methodology === 'Visual Inspection' && !$record->includeXrf) {
                $pdfFiles[] = $this->generatePdf('pdf.visual', $record, "visual_report_{$record->id}.pdf", 'portrait', $signatureUrl);
                $this->addIfValid($pdfFiles, storage_path("app/public/{$record->floor_plan}"));
                if ($record->pass_fail === 'pass') {
                    $certificateFiles[] = $this->generatePdf('pdf.certificate', $record, "certificate_{$record->id}.pdf", 'landscape', $signatureUrl);
                }
            }

            // Condition 2: Dust Wipe Sampling, No XRF
            if ($record->methodology === 'Dust Wipe Sampling' && !$record->includeXrf) {
                $pdfFiles[] = $this->generatePdf('pdf.dust', $record, "dust_report_{$record->id}.pdf", 'portrait', $signatureUrl);
                $this->addIfValid($pdfFiles, storage_path("app/public/{$record->floor_plan}"));
                $this->addIfValid($pdfFiles, storage_path("app/public/{$record->lab_report}"));
                if ($record->pass_fail === 'pass') {
                    $certificateFiles[] = $this->generatePdf('pdf.certificate', $record, "certificate_{$record->id}.pdf", 'landscape', $signatureUrl);
                }
            }

            // Condition 3: XRF Pass
            if (($record->methodology === 'Visual Inspection' || $record->methodology === 'Dust Wipe Sampling') && 
                $record->includeXrf && 
                $record->xrf_pass_fail === 'pass') {
                $xrfPdfPath = $this->generatePdf('pdf.xrf', $record, "xrf_report_{$record->id}.pdf", 'portrait', $signatureUrl);
                $pdfFiles[] = $xrfPdfPath;
                $this->addIfValid($pdfFiles, storage_path("app/public/{$record->floor_plan}"));

                $xrfReportPath = storage_path("app/public/{$record->xrf_report}");
                if ($this->addIfValid($certificateFiles, $xrfReportPath)) {
                    \Log::debug("Added existing xrf_report to certificateFiles for Condition 3, Record ID: {$record->id}", ['path' => $xrfReportPath]);
                } else {
                    \Log::warning("Failed to add existing xrf_report for Condition 3, Record ID: {$record->id}", ['path' => $xrfReportPath]);
                }

                $certificateFreePath = $this->generatePdf('pdf.certificatefree', $record, "certificatefree_{$record->id}.pdf", 'landscape', $signatureUrl);
                if (file_exists($certificateFreePath)) {
                    $certificateFiles[] = $certificateFreePath;
                    \Log::debug("Added certificatefree to certificateFiles for Condition 3, Record ID: {$record->id}", ['path' => $certificateFreePath]);
                } else {
                    \Log::error("Failed to generate certificatefree: {$certificateFreePath}");
                }
            }

            // Condition 4: XRF Fail
            if (($record->methodology === 'Visual Inspection' || $record->methodology === 'Dust Wipe Sampling') && 
                $record->includeXrf && 
                $record->xrf_pass_fail === 'fail') {
                $xrfPdfPath = $this->generatePdf('pdf.xrf', $record, "xrf_report_{$record->id}.pdf", 'portrait', $signatureUrl);
                $pdfFiles[] = $xrfPdfPath;
                $this->addIfValid($pdfFiles, storage_path("app/public/{$record->floor_plan}"));

                $xrfReportPath = storage_path("app/public/{$record->xrf_report}");
                if ($this->addIfValid($certificateFiles, $xrfReportPath)) {
                    \Log::debug("Added existing xrf_report to certificateFiles for Condition 4, Record ID: {$record->id}", ['path' => $xrfReportPath]);
                } else {
                    \Log::warning("Failed to add existing xrf_report for Condition 4, Record ID: {$record->id}", ['path' => $xrfReportPath]);
                }

                if ($record->methodology === 'Visual Inspection') {
                    $pdfFiles[] = $this->generatePdf('pdf.visual', $record, "visual_report_{$record->id}.pdf", 'portrait', $signatureUrl);
                    if ($record->pass_fail === 'pass') {
                        $certificateFiles[] = $this->generatePdf('pdf.certificate', $record, "certificate_{$record->id}.pdf", 'landscape', $signatureUrl);
                    }
                } elseif ($record->methodology === 'Dust Wipe Sampling') {
                    $pdfFiles[] = $this->generatePdf('pdf.dust', $record, "dust_report_{$record->id}.pdf", 'portrait', $signatureUrl);
                    $this->addIfValid($pdfFiles, storage_path("app/public/{$record->lab_report}"));
                    if ($record->pass_fail === 'pass') {
                        $certificateFiles[] = $this->generatePdf('pdf.certificate', $record, "certificate_{$record->id}.pdf", 'landscape', $signatureUrl);
                    }
                }
            }

            $finalPdfPath = storage_path("app/public/reports/{$fileName}.pdf");
            \Log::debug("Merging PDFs for Record ID: {$record->id}", [
                'pdfFiles' => $pdfFiles,
                'certificateFiles' => $certificateFiles,
                'outputPath' => $finalPdfPath
            ]);
            $this->mergePdfs($pdfFiles, $certificateFiles, $finalPdfPath);

            if (!file_exists($finalPdfPath)) {
                \Log::error("Final PDF not generated: {$finalPdfPath}");
                return false;
            }

            $this->cleanupTempFiles(array_merge($pdfFiles, $certificateFiles));
            return Storage::disk('public')->exists("reports/{$fileName}.pdf") ? "reports/{$fileName}.pdf" : false;
        } catch (\Exception $e) {
            \Log::error("PDF Generation Failed: " . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return false;
        }
    }

    private function generatePdf($view, $record, $fileName, $orientation = 'portrait', $signatureUrl = null)
    {
        $data = compact('record');
        if ($signatureUrl) {
            $data['signatureUrl'] = $signatureUrl;
        }
        $pdf = PDF::loadView($view, $data)->setPaper('a4', $orientation);
        $pdfPath = storage_path("app/private/temp/{$fileName}");
        $pdf->save($pdfPath);
        if (file_exists($pdfPath)) {
            \Log::debug("Generated PDF: {$pdfPath}");
        } else {
            \Log::error("Failed to generate PDF: {$pdfPath}");
        }
        return $pdfPath;
    }

    private function addIfValid(&$array, $path)
    {
        if ($this->isValidPdfFile($path)) {
            $array[] = $path;
            return true;
        }
        return false;
    }

    private function cleanupTempFiles($files)
    {
        foreach ($files as $file) {
            if (strpos($file, 'temp') !== false && file_exists($file)) {
                @unlink($file);
                \Log::debug("Cleaned up temporary file: {$file}");
            }
        }
    }

    private function isValidPdfFile($path)
    {
        if (empty($path) || !is_string($path)) {
            \Log::warning("Invalid path provided: " . json_encode($path));
            return false;
        }
        if (!file_exists($path)) {
            \Log::warning("File does not exist: {$path}");
            return false;
        }
        if (is_dir($path)) {
            \Log::warning("Path is a directory, not a file: {$path}");
            return false;
        }
        if (!is_readable($path)) {
            \Log::warning("File is not readable: {$path}");
            return false;
        }
        $mime = mime_content_type($path);
        if ($mime !== 'application/pdf') {
            \Log::warning("File is not a PDF: {$path}, MIME type: {$mime}");
            return false;
        }
        \Log::debug("Validated PDF file: {$path}");
        return true;
    }

    private function mergePdfs(array $pdfFiles, array $certificateFiles, $outputPath)
{
    $pdf = new Fpdi();

    foreach ($pdfFiles as $file) {
        if ($this->isValidPdfFile($file)) {
            try {
                $pageCount = $pdf->setSourceFile($file);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tplIdx);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplIdx);
                }
                \Log::debug("Merged PDF with original orientation: {$file}");
            } catch (\Exception $e) {
                \Log::error("Failed to merge PDF file {$file}: " . $e->getMessage());
            }
        }
    }

    foreach ($certificateFiles as $file) {
        if ($this->isValidPdfFile($file)) {
            try {
                $pageCount = $pdf->setSourceFile($file);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplIdx = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tplIdx);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplIdx);
                }
                \Log::debug("Merged PDF with original orientation: {$file}");
            } catch (\Exception $e) {
                \Log::error("Failed to merge certificate PDF file {$file}: " . $e->getMessage());
            }
        }
    }

    $outputDir = dirname($outputPath);
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    $pdf->Output($outputPath, 'F');
    \Log::info("PDF merging completed: {$outputPath}");
}
}