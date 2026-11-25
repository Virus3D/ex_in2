<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\FnsReceipt;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class FnsReceiptCrudController extends AbstractCrudController
{
    /**
     * @inheritDoc
     */
    public static function getEntityFqcn(): string
    {
        return FnsReceipt::class;
    }// end getEntityFqcn()

    /**
     * {@inheritDoc}
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->showEntityActionsInlined();
    }// end configureCrud()

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('receiptKey')->setLabel('Ключ ФНС');
        yield TextField::new('kktOwner')->setLabel('Владелец ККТ');
        yield DateTimeField::new('fiscalDate')->setLabel('Дата фискализации');
        yield MoneyField::new('totalSum')
            ->setLabel('Итого')
            ->setCurrency('RUB')
            ->setRequired(false);

        yield AssociationField::new('items')
            ->setLabel('Товары')
            ->onlyOnDetail();
    }// end configureFields()

    /**
     * @inheritDoc
     */
    public function configureActions(Actions $actions): Actions
    {
        $linkItemsAction = Action::new('linkItems', 'Редактировать товары', 'fas fa-shopping-cart')
            ->linkToCrudAction('linkToItems');

        return $actions
            // Только просмотр — без создания и редактирования.
            ->disable(Action::NEW, Action::EDIT, Action::DELETE, Action::BATCH_DELETE)
            ->add(Crud::PAGE_DETAIL, $linkItemsAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }// end configureActions()

    /**
     * Перенаправление на список товаров чека.
     */
    #[AdminRoute(path: '/link-items/{entityId}', name: 'app_admin_fns_receipt_link_items', options: ['methods' => ['GET']])]
    public function linkToItems(
        AdminContext $context,
        AdminUrlGeneratorInterface $adminUrlGenerator,
    ): RedirectResponse {
        $receiptId = $context->getEntity()->getInstance()->getId();

        $url = $adminUrlGenerator
            ->unsetAll()
            ->setController(FnsReceiptItemCrudController::class)
            ->setAction(Crud::PAGE_INDEX)
            ->set('receiptId', $receiptId)
            ->generateUrl();

        return $this->redirect($url);
    }// end linkToItems()
}// end class
