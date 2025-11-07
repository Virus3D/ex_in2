<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\ReceiptType;
use App\Repository\ReceiptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ReceiptForm extends AbstractController
{
    use ComponentToolsTrait;

    use ComponentWithFormTrait;

    use DefaultActionTrait;

    /**
     * Список уникальных комментариев.
     *
     * @var list<string>
     */
    private array $existingComments = [];

    public function __construct(
        private readonly ReceiptRepository $receiptRepository,
    ) {
    }//end __construct()

    /**
     * Инициализация формы.
     */
    protected function instantiateForm(): FormInterface
    {
        $this->existingComments = $this->receiptRepository->getUniqueComments();

        return $this->createForm(ReceiptType::class);
    }//end instantiateForm()

    /**
     * Метод для получения существующих комментариев (доступен в шаблоне).
     *
     * @return list<string>
     */
    public function getExistingComments(): array
    {
        return $this->existingComments;
    }//end getExistingComments()

    /**
     * Сохранение формы.
     */
    #[LiveAction]
    public function save(EntityManagerInterface $entityManager): void
    {
        $this->submitForm();

        $receipt = $this->getForm()->getData();
        $entityManager->persist($receipt);
        $entityManager->flush();

        $this->emit('receiptAdded');
    }//end save()
}//end class
