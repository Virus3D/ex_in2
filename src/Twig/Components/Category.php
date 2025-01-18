<?php

/**
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
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Category
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $categoryID;

    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack, private CategoryService $categoryService)
    {
        $this->entityManager = $entityManager;
        $this->requestStack  = $requestStack;
    }//end __construct()

    public function getCategory(): ?CardCategory
    {
        $category = $this->entityManager->getRepository(CardCategory::class)->find($this->categoryID);
        $request  = $this->requestStack->getCurrentRequest();

        FilterDataHelper::getFilterData($request);

        $this->categoryService->handle($category);

        return $category;
    }//end getCategory()
}//end class
