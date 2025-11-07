<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Subscription;
use App\Service\DueSubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class SubscriptionList
{
    use DefaultActionTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private DueSubscriptionService $dueSubscriptionService,
    ) {
        $this->dueSubscriptionService->execute();
    }//end __construct()

    /**
     * Получить список подписок.
     *
     * @return Subscription[]
     */
    public function getSubscriptionList(): array
    {
        return $this->entityManager->getRepository(Subscription::class)->findBy([], ['nextPaymentDate' => 'asc']);
    }//end getSubscriptionList()

    /**
     * Добавить расход.
     */
    #[LiveListener('subscriptionAdded')]
    public function onSubscriptionAdded(): void
    {
        $this->dueSubscriptionService->execute();
    }//end onSubscriptionAdded()
}//end class
