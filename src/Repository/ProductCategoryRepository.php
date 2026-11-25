<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProductCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductCategory>
 */
final class ProductCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCategory::class);
    }// end __construct()

    /**
     * Найти категорию по имени (регистронезависимо).
     */
    public function findByName(string $name): ?ProductCategory
    {
        return $this->createQueryBuilder('pc')
            ->andWhere('LOWER(pc.name) = LOWER(:name)')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }// end findByName()

    /**
     * Создать категорию или обновить существующую.
     */
    public function upsert(string $name, ?string $keywords): ProductCategory
    {
        $category = $this->findByName($name);

        if ($category === null) {
            $category = new ProductCategory();
            $category->setName($name);
        }

        if ($keywords !== null) {
            $category->setKeywords($keywords);
        }

        return $category;
    }// end upsert()
}// end class
