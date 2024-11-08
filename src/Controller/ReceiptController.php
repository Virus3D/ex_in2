<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Receipt;
use App\Form\ReceiptType;
use App\Helper\FilterDataHelper;
use App\Service\CardService;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

final class ReceiptController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    #[Route('/receipt/add', name: 'app_receipt_add', methods: ['POST'])]
    public function add(Request $request, CardService $cardService, CategoryService $categoryService): Response
    {
        $receipt     = new Receipt();
        $formReceipt = $this->createForm(
            ReceiptType::class,
            $receipt,
            [
                'action' => $this->generateUrl('app_receipt_add'),
            ]
        );

        $formReceipt->handleRequest($request);

        if ($formReceipt->isSubmitted() && $formReceipt->isValid())
        {
            $this->entityManager->persist($receipt);
            $this->entityManager->flush();
            $cardService->changeBalance($receipt->getCard(), $receipt->getBalance());

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
            {
                FilterDataHelper::getFilterData($request);

                $category = $receipt->getCard()->getCategory();
                $categoryService->handle($category);

                // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
                return $this->render(
                    'receipt/combo.html.twig',
                    [
                        'category'    => $category,
                        'receiptList' => $this->getReceiptList($request),
                    ]
                );
            }
        }//end if

        return $this->redirectToRoute('app_main', [], 303);
    }//end add()

    #[Route('/receipt', name: 'app_receipt_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        return $this->render(
            'receipt/list.html.twig',
            [
                'receiptList' => $this->getReceiptList($request),
            ]
        );
    }//end list()

    /**
     * @return Receipt[]
     */
    private function getReceiptList(Request $request): array
    {
        FilterDataHelper::getFilterData($request);

        return $this->entityManager->getRepository(Receipt::class)->list(FilterDataHelper::$startDate, FilterDataHelper::$endDate);
    }//end getReceiptList()
}//end class
