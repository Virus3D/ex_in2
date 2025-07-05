<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\CardCategory;
use App\Helper\FilterDataHelper;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Category
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $categoryID;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private CategoryService $categoryService,
    ) {}//end __construct()

    /**
     * Возвращает информацию по картам категории.
     */
    public function getCategory(): ?CardCategory
    {
        $category = $this->entityManager->getRepository(CardCategory::class)->find($this->categoryID);
        $request  = $this->requestStack->getCurrentRequest();

        $this->categoryService->handle($category);

        return $category;
    }//end getCategory()

    #[LiveListener('receiptAdded')]
    #[LiveListener('spendAdded')]
    #[LiveListener('transferAdded')]
    #[LiveListener('receiptDeleted')]
    #[LiveListener('spendDeleted')]
    #[LiveListener('transferDeleted')]
    #[LiveListener('subscriptionAdded')]
    public function onEvent(): void
    {}//end onEvent()
}//end class
