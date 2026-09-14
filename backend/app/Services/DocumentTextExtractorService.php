<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class DocumentTextExtractorService
{
    public function extract(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->extractFromPdf($file),
            'docx', 'doc' => $this->extractFromWord($file),
            default => throw new RuntimeException("Formato no soportado: {$extension}. Solo se aceptan PDF y Word."),
        };
    }

    protected function extractFromPdf(UploadedFile $file): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($file->getRealPath());

        $text = $pdf->getText();

        return $this->normalize($text);
    }

    protected function extractFromWord(UploadedFile $file): string
    {
        $phpWord = WordIOFactory::load($file->getRealPath());

        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $text .= $child->getText() . ' ';
                        }
                    }
                    $text .= "\n";
                }
            }
        }

        return $this->normalize($text);
    }

    protected function normalize(string $text): string
    {
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = trim($text);

        $maxChars = 60000;
        if (mb_strlen($text) > $maxChars) {
            $text = mb_substr($text, 0, $maxChars) . "\n\n[...documento truncado...]";
        }

        return $text;
    }
}