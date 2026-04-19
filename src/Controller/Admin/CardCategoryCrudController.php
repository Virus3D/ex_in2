<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\CardCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

final class CardCategoryCrudController extends AbstractCrudController
{
    /**
     * {@inheritDoc}
     */
    public static function getEntityFqcn(): string
    {
        return CardCategory::class;
    }// end getEntityFqcn()
}// end class
