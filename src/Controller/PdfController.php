<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Form\PdfUploadType;
use App\Service\BankStatementParser;
use App\Service\PdfParserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PdfController extends AbstractController
{
    #[Route('/parse-pdf', name: 'parse_pdf')]
    public function parsePdf(
        Request $request,
        PdfParserService $pdfParser,
        BankStatementParser $bankStatementParser,
    ): Response {
        $form = $this->createForm(PdfUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $form->get('pdf')->getData();

            // try {
            $text = $pdfParser->parsePdf($pdfFile);
            echo '<pre>'.$text.'</pre>';
            var_dump($bankStatementParser->parse($text));
            exit;

            return $this->json(
                [
                    'status'  => 'success',
                    'content' => $text,
                ]
            );
            // }
            // catch (Exception $e) {
            //     return $this->json(
            //         [
            //             'status'  => 'error',
            //             'message' => $e->getMessage(),
            //         ],
            //         500
            //     );
            // }
        }//end if

        return $this->json(['error' => 'File not loaded'], 400);
    }//end parsePdf()
}//end class
