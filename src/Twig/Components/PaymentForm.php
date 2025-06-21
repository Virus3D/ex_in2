<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Place;
use App\Entity\ServicePayment;
use App\Entity\Spend;
use App\Form\ServicePaymentType;
use App\Helper\FilterDataHelper;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PaymentForm extends AbstractController
{
    use ComponentToolsTrait;

    use ComponentWithFormTrait;

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $placeId;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    ) {
    }//end __construct()

    /**
     * Инициализация формы.
     */
    protected function instantiateForm(): FormInterface
    {
        $place = $this->entityManager->getRepository(Place::class)->find($this->placeId);

        return $this->createForm(ServicePaymentType::class, options: ['place' => $place]);
    }//end instantiateForm()

    /**
     * Сохранение формы.
     */
    #[LiveAction]
    public function save(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        FilterDataHelper::getFilterData($request);

        $this->submitForm();

        $place = $this->entityManager->getRepository(Place::class)->find($this->placeId);

        $formPayment    = $this->getForm();
        $date           = new DateTime();
        $servicePayment = new ServicePayment();
        $servicePayment
            ->setPlace($place)
            ->setService($formPayment->get('service')->getData())
            ->setYear(FilterDataHelper::$year)
            ->setMonth($formPayment->get('month')->getData())
            ->setAmount($formPayment->get('amount')->getData())
            ->setDate($date);
        $this->entityManager->persist($servicePayment);

        $spend = new Spend();
        $spend
            ->setDate($date)
            ->setCard($formPayment->get('card')->getData())
            ->setBalance($formPayment->get('amount')->getData())
            ->setComment('Service: ' . $formPayment->get('service')->getData()->getName());
        $this->entityManager->persist($spend);
        $this->entityManager->flush();

        $this->emit('paymentAdded');
        $this->emit('spendAdded');
    }//end save()
}//end class
