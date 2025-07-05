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
use App\Form\ServiceAccountType;
use App\Helper\FilterDataHelper;
use App\Repository\PlaceRepository;
use App\Service\ServiceAccountService;
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
        private RequestStack $requestStack,
        private ServiceAccountService $serviceAccountService,
        private PlaceRepository $placeRepository,
        private FilterDataHelper $filterDataHelper,
    ) {}//end __construct()

    /**
     * Инициализация формы.
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ServiceAccountType::class, options: ['place' => $this->getPlace()]);
    }//end instantiateForm()

    /**
     * Сохранение формы.
     */
    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();

        $data = $this->getForm()->getData();

        $request = $this->requestStack->getCurrentRequest();

        $this->serviceAccountService->createAccount(
            $this->getPlace(),
            $data,
            $this->filterDataHelper->year
        );

        $this->emit('accountAdded');
    }//end save()

    private function getPlace(): Place
    {
        return $this->placeRepository->find($this->placeId);
    }//end getPlace()
}//end class
