<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use Exception;
use RuntimeException;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

final class PdfParserService
{
    public function __construct(private readonly Parser $parser) {}//end __construct()

    /**
     * Парсинг файла PDF.
     */
    public function parsePdf(File $file): string
    {
        if ('application/pdf' !== $file->getMimeType()) {
            throw new UnsupportedMediaTypeHttpException('Only PDF files are allowed!');
        }

        try {
            $pdf = $this->parser->parseFile($file->getPathname());

            return $pdf->getText();
        }
        catch (Exception $e) {
            throw new RuntimeException('Error PDF parser: '.$e->getMessage());
        }
    }//end parsePdf()
}//end class
