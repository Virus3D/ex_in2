<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Service\CardService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class CardCrudController extends AbstractCrudController
{
    /**
     * @inheritDoc
     */
    public static function getEntityFqcn(): string
    {
        return Card::class;
    }// end getEntityFqcn()

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield AssociationField::new('category');
        yield ChoiceField::new('type')->setChoices(
            [
                'type.debit_card'      => CardService::DEBIT_CARD,
                'type.credit_card'     => CardService::CREDIT_CARD,
                'type.savings_account' => CardService::SAVINGS_ACCOUNT,
                'type.credit'          => CardService::CREDIT,
            ]
        );
        yield MoneyField::new('balance')->setCurrency('RUB');
    }// end configureFields()
}// end class
