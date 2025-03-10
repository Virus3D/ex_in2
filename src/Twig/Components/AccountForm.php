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
use App\Entity\ServiceAccount;
use App\Form\ServiceAccountType;
use App\Helper\FilterDataHelper;
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
final class AccountForm extends AbstractController
{
    use ComponentToolsTrait;

    use ComponentWithFormTrait;

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $placeId;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    ) {}//end __construct()

    /**
     * Инициализация формы.
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ServiceAccountType::class);
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

        $formAccount    = $this->getForm();
        $serviceAccount = new ServiceAccount();
        $serviceAccount
            ->setPlace($place)
            ->setService($formAccount->get('service')->getData())
            ->setYear(FilterDataHelper::$year)
            ->setMonth($formAccount->get('month')->getData())
            ->setAmount($formAccount->get('amount')->getData());
        $this->entityManager->persist($serviceAccount);
        $this->entityManager->flush();

        $this->emit('accountAdded');
    }//end save()
}//end class
