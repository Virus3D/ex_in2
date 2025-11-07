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
use App\Entity\Service;
use App\Helper\FilterDataHelper;
use App\Service\ServiceAccountService;
use App\Service\ServicePaymentService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ServicesView
{
    use ComponentToolsTrait;

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $placeId;

    /**
     * Счета услуг по месяцам
     *
     * @var array<int, array<int, int>>
     */
    public array $accounts = [];

    /**
     * Платежи услуг по месяцам
     *
     * @var array<int, array<int, array<mixed>>>
     */
    public array $payments = [];

    /**
     * Итого по услугам
     *
     * @var array<int, array<string, int>>
     */
    public array $total = [];

    /**
     * Итого по месяцам
     *
     * @var array<int, int>
     */
    public array $totalMonth = [];

    public int $totalTotal = 0;

    public int $totalDebt = 0;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private ServiceAccountService $serviceAccountService,
        private ServicePaymentService $servicePaymentService,
        private FilterDataHelper $filterDataHelper
    ) {
        $this->totalMonth = array_fill(1, 12, 0);
    }//end __construct()

    /**
     * Получить список.
     *
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        $request = $this->requestStack->getCurrentRequest();

        $place = $this->entityManager->getRepository(Place::class)->findWithService($this->placeId);

        $services = $place->getServices();

        $this->accounts   = [];
        $this->payments   = [];
        $this->total      = [];
        $this->totalMonth = array_fill(1, 12, 0);

        $accountDB = $this->serviceAccountService->handle($place, $this->filterDataHelper->year);
        $paymentDB = $this->servicePaymentService->handle($place, $this->filterDataHelper->year);

        foreach ($services as $service) {
            $serviceId = $service->getId();

            $this->accounts[$serviceId] = ($accountDB[$serviceId] ?? []) + array_fill(1, 12, 0);
            $this->payments[$serviceId] = ($paymentDB[$serviceId] ?? []) + array_fill(1, 12, []);

            $paymentTotal = 0;
            foreach ($this->payments[$serviceId] as $month => $payments) {
                $paymentTotal += array_sum(array_column($payments, 'amount'));
            }

            $totalService = array_sum($this->accounts[$serviceId]);

            $this->total[$serviceId] = [
                'total' => $totalService,
                'debt'  => (int) $totalService - $paymentTotal,
            ];

            foreach ($this->accounts[$serviceId] as $month => $value) {
                $this->totalMonth[$month] += $value;
            }
        }//end foreach

        $this->totalTotal = array_sum(array_column($this->total, 'total'));
        $this->totalDebt  = array_sum(array_column($this->total, 'debt'));

        return $services;
    }//end getServices()

    /**
     * Обновить таблицу.
     */
    #[LiveListener('accountAdded')]
    #[LiveListener('paymentAdded')]
    public function onAdded(): void {}//end onSubscriptionAdded()
}//end class
