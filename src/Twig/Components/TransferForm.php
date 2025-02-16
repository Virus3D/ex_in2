<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\TransferType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class TransferForm extends AbstractController
{
    use ComponentToolsTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    /**
     * Инициализация формы.
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(TransferType::class);
    }//end instantiateForm()

    /**
     * Сохранение формы.
     */
    #[LiveAction]
    public function save(EntityManagerInterface $entityManager): void
    {
        $this->submitForm();

        $transfer = $this->getForm()->getData();
        $entityManager->persist($transfer);
        $entityManager->flush();

        $this->addFlash('success', 'Post saved!');

        $this->emit('transferAdded');
    }//end save()
}//end class
