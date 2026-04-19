<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Subscription;
use App\Enum\Period;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class SubscriptionCrudController extends AbstractCrudController
{
    /**
     * @inheritDoc
     */
    public static function getEntityFqcn(): string
    {
        return Subscription::class;
    }// end getEntityFqcn()

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield MoneyField::new('amount')->setCurrency('RUB');
        yield MoneyField::new('balance')->setCurrency('RUB')->setFormTypeOptions(
            ['required' => false]
        );
        yield ChoiceField::new('period')
            ->setChoices(Period::cases())
            ->allowMultipleChoices(false)
            ->setEmptyData(Period::month);
        yield DateField::new('nextPaymentDate');
        yield BooleanField::new('isActive', 'Is Active');
    }// end configureFields()
}// end class
