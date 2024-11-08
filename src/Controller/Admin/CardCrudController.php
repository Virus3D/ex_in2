<?php

/**
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
    public static function getEntityFqcn(): string
    {
        return Card::class;
    }//end getEntityFqcn()

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('Name');
        yield AssociationField::new('category');
        yield ChoiceField::new('type')->setChoices(
            [
                'Debit card'      => CardService::DEBIT_CARD,
                'Credit card'     => CardService::CREDIT_CARD,
                'Savings account' => CardService::SAVINGS_ACCOUNT,
                'Credit'          => CardService::CREDIT,
            ]
        );
        yield MoneyField::new('balance')->setCurrency('RUB');
    }//end configureFields()
}//end class
