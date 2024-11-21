<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardCategory;
use App\Entity\Transfer;
use App\Form\TransferType;
use App\Helper\FilterDataHelper;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

final class TransferController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    #[Route('/transfer/add', name: 'app_transfer_add', methods: ['POST'])]
    public function add(Request $request, CategoryService $categoryService): Response
    {
        $transfer     = new Transfer();
        $formTransfer = $this->createForm(
            TransferType::class,
            $transfer,
            [
                'action' => $this->generateUrl('app_transfer_add'),
            ]
        );

        $formTransfer->handleRequest($request);

        if ($formTransfer->isSubmitted() && $formTransfer->isValid())
        {
            $this->entityManager->persist($transfer);
            $this->entityManager->flush();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
            {
                FilterDataHelper::getFilterData($request);

                $categories = $this->getCategories($transfer);
                foreach ($categories as $category)
                {
                    $categoryService->handle($category);
                }

                // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
                return $this->render(
                    'transfer/combo.html.twig',
                    [
                        'categories'   => $categories,
                        'transferList' => $this->getTransferList($request),
                    ]
                );
            }//end if
        }//end if

        return $this->redirectToRoute('app_main', [], 303);
    }//end add()

    #[Route('/transfer', name: 'app_transfer_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        return $this->render(
            'transfer/list.html.twig',
            [
                'transferList' => $this->getTransferList($request),
            ]
        );
    }//end list()

    /**
     * @return Transfer[]
     */
    private function getTransferList(Request $request): array
    {
        FilterDataHelper::getFilterData($request);

        return $this->entityManager->getRepository(Transfer::class)->list(FilterDataHelper::$startDate, FilterDataHelper::$endDate);
    }//end getTransferList()

    /**
     * @return array<CardCategory>
     */
    private function getCategories(Transfer $transfer): array
    {
        $categoryIn  = $transfer->getCardIn()->getCategory();
        $categoryOut = $transfer->getCardOut()->getCategory();

        return ($categoryIn->getId() === $categoryOut->getId()) ? [$categoryIn] : [$categoryIn, $categoryOut];
    }//end getCategories()
}//end class
