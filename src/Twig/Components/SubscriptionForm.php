<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Spend;
use App\Entity\SubscriptionPayment;
use App\Form\SubscriptionPaymentType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubscriptionForm extends AbstractController
{
    use ComponentToolsTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    /**
     * Инициализация формы.
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SubscriptionPaymentType::class);
    }//end instantiateForm()

    /**
     * Сохранение формы.
     */
    #[LiveAction]
    public function save(EntityManagerInterface $entityManager): void
    {
        $this->submitForm();

        $form = $this->getForm();
        $date = new DateTime();

        $amount         = $form->get('amount')->getData();
        $subscription   = $form->get('subscrip')->getData();
        $servicePayment = new SubscriptionPayment();
        $servicePayment
            ->setCard($form->get('card')->getData())
            ->setSubscrip($subscription)
            ->setAmount($amount)
            ->setDate($date);

        $entityManager->persist($servicePayment);

        $subscription->setBalance($subscription->getBalance() + $amount);

        $spend = new Spend();
        $spend
            ->setDate($date)
            ->setCard($form->get('card')->getData())
            ->setBalance($amount)
            ->setComment('Sub: '.$subscription->getName());

        $entityManager->persist($spend);

        $entityManager->flush();

        $this->emit('subscriptionAdded');
    }//end save()
}//end class
