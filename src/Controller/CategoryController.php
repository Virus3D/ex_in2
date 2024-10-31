<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CardCategory;
use App\Helper\FilterDataHelper;
use App\Service\ReceiptService;
use App\Service\SpendService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_category_view')]
    public function view(CardCategory $category, Request $request, ReceiptService $receiptService, SpendService $spendService): Response
    {
        FilterDataHelper::getFilterData($request);

        $totalReceipt = $receiptService->getCardsSummary($category->getCards(), FilterDataHelper::$startDate, FilterDataHelper::$endDate);
        $totalSpend   = $spendService->getCardsSummary($category->getCards(), FilterDataHelper::$startDate, FilterDataHelper::$endDate);

        return $this->render(
            'category/view.html.twig',
            [
                'category'     => $category,
                'totalReceipt' => $totalReceipt,
                'totalSpend'   => $totalSpend,
            ]
        );
    }//end view()
}//end class
