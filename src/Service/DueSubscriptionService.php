<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Subscription;
use App\Enum\Period;
use App\Exception\UnknownPeriodException;
use App\Repository\SubscriptionRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

final class DueSubscriptionService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SubscriptionRepository $subscriptionRepository,
    ) {}//end __construct()

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }//end setLogger()

    public function execute(): void
    {
        $subscriptions = $this->subscriptionRepository->findAllDueSubscriptions(new DateTime());

        foreach ($subscriptions as $subscription)
        {
            try
            {
                $this->processSubscription($subscription);
            }
            catch (UnknownPeriodException $e)
            {
                $this->logger->error("Error processing subscription {$subscription->getId()}: {$e->getMessage()}");
            }
        }
    }//end execute()

    private function processSubscription(Subscription $subscription): void
    {
        $currentBalance = $subscription->getBalance();
        $amountToDeduct = $subscription->getAmount();

        $newBalance = $currentBalance - $amountToDeduct;
        $subscription->setBalance($newBalance);

        try
        {
            $nextPaymentDate = $this->calculateNextPaymentDate($subscription);
            $subscription->setNextPaymentDate($nextPaymentDate);
        }
        catch (UnknownPeriodException $e)
        {
            $this->logger->error(
                "Error calculating next payment date for subscription {$subscription->getId()}: {$e->getMessage()}"
            );

            return;
        }

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
        $this->logger->info("Processed subscription {$subscription->getId()} successfully.");
    }//end processSubscription()

    private function calculateNextPaymentDate(Subscription $subscription)
    {
        $currentDate = clone $subscription->getNextPaymentDate();
        switch ($subscription->getPeriod())
        {
            case Period::month:
                $interval = new DateInterval('P1M');

                break;

            case Period::year:
                $interval = new DateInterval('P1Y');

                break;
            default:
                throw new UnknownPeriodException();
        }

        return $currentDate->add($interval);
    }//end calculateNextPaymentDate()
}//end class
