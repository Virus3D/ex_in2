<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\FnsReceiptItem;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\QueryBuilder;

final class FnsReceiptItemCrudController extends AbstractCrudController
{
    /**
     * @inheritDoc
     */
    public static function getEntityFqcn(): string
    {
        return FnsReceiptItem::class;
    }// end getEntityFqcn()

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield MoneyField::new('price')->setRequired(false)->setCurrency('RUB');
        yield NumberField::new('quantity')->setRequired(false);
        yield MoneyField::new('sum')->setRequired(false)->setCurrency('RUB');
        yield AssociationField::new('category')
            ->setRequired(false)
            ->setHelp('Выберите категорию товара');
    }// end configureFields()

    /**
     * @inheritDoc
     */
    public function configureActions(actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE);
    }// end configureActions()

    /**
     * Фильтруем элементы по receipt_id при переходе из detail чека.
     */
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Если передан параметр receipt_id, фильтруем по нему.
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request === null) {
            return $queryBuilder;
        }

        $receiptId = $request->query->get('receiptId');

        if ($receiptId !== null && $receiptId !== '') {
            $queryBuilder
                ->andWhere('entity.receipt = :receiptId')
                ->setParameter('receiptId', $receiptId);
        }

        return $queryBuilder;
    }// end createIndexQueryBuilder()
}// end class
