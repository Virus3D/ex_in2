<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Spend;
use App\Form\SpendType;
use App\Helper\FilterDataHelper;
use App\Service\CardService;
use App\Service\ReceiptService;
use App\Service\SpendService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

final class SpendController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}//end __construct()

    #[Route('/spend/add', name: 'app_spend_add', methods: ['POST'])]
    public function add(Request $request, CardService $cardService, ReceiptService $receiptService, SpendService $spendService): Response
    {
        $spend     = new Spend();
        $formSpend = $this->createForm(
            SpendType::class,
            $spend,
            [
                'action' => $this->generateUrl('app_spend_add'),
            ]
        );

        $formSpend->handleRequest($request);

        if ($formSpend->isSubmitted() && $formSpend->isValid())
        {
            $this->entityManager->persist($spend);
            $this->entityManager->flush();
            $cardService->changeBalance($spend->getCard(), -$spend->getBalance());

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
            {
                FilterDataHelper::getFilterData($request);

                $category     = $spend->getCard()->getCategory();
                $totalReceipt = $receiptService->getCardsSummary($category->getCards(), FilterDataHelper::$startDate, FilterDataHelper::$endDate);
                $totalSpend   = $spendService->getCardsSummary($category->getCards(), FilterDataHelper::$startDate, FilterDataHelper::$endDate);

                // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
                return $this->render(
                    'spend/combo.html.twig',
                    [
                        'category'     => $category,
                        'totalReceipt' => $totalReceipt,
                        'totalSpend'   => $totalSpend,
                        'spendList'    => $this->getSpendList($request),
                    ]
                );
            }
        }//end if

        return $this->redirectToRoute('app_main', [], 303);
    }//end add()

    #[Route('/spend', name: 'app_spend_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        return $this->render(
            'spend/list.html.twig',
            [
                'spendList' => $this->getSpendList($request),
            ]
        );
    }//end list()

    /**
     * @return Spend[]
     */
    private function getSpendList(Request $request): array
    {
        FilterDataHelper::getFilterData($request);

        return $this->entityManager->getRepository(Spend::class)->list(FilterDataHelper::$startDate, FilterDataHelper::$endDate);
    }//end getSpendList()
}//end class
