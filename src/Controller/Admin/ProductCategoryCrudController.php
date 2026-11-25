<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class ProductCategoryCrudController extends AbstractCrudController
{
    /**
     * @inheritDoc
     */
    public static function getEntityFqcn(): string
    {
        return ProductCategory::class;
    }// end getEntityFqcn()

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield CodeEditorField::new('keywords')
            ->setFormTypeOption('attr.placeholder', 'продукты, еда, продукты питания')
            ->setRequired(false);
    }// end configureFields()
}// end class
