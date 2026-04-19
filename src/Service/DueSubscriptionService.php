<?php

/**
 * Expenses/Income
 *
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

/**
 * Сервис для обработки подписок, у которых наступил срок платежа.
 *
 * Выполняет списание средств с баланса подписки, обновляет баланс,
 * пересчитывает следующую дату платежа и сохраняет изменения в БД.
 */
final class DueSubscriptionService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SubscriptionRepository $subscriptionRepository,
    ) {
    }// end __construct()

    /**
     * Устанавливает логгер для сервиса.
     *
     * @param LoggerInterface $logger Экземпляр логгера.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }// end setLogger()

    /**
     * Основной метод выполнения: находит все просроченные подписки
     * и обрабатывает их.
     */
    public function execute(): void
    {
        $subscriptions = $this->subscriptionRepository->findAllDueSubscriptions(new DateTime());

        foreach ($subscriptions as $subscription) {
            try {
                $this->processSubscription($subscription);
            } catch (UnknownPeriodException $e) {
                $this->logger->error("Error processing subscription {$subscription->getId()}: {$e->getMessage()}");
            }
        }
    }// end execute()

    /**
     * Обрабатывает одну подписку: списывает сумму, обновляет баланс
     * и пересчитывает следующую дату платежа.
     *
     * @param Subscription $subscription Обрабатываемая подписка.
     */
    private function processSubscription(Subscription $subscription): void
    {
        $currentBalance = $subscription->getBalance();
        $amountToDeduct = $subscription->getAmount();

        // Рассчитываем новый баланс после списания.
        $newBalance = $currentBalance - $amountToDeduct;
        $subscription->setBalance($newBalance);

        try {
            // Вычисляем следующую дату платежа на основе периода подписки.
            $nextPaymentDate = $this->calculateNextPaymentDate($subscription);
            $subscription->setNextPaymentDate($nextPaymentDate);
        } catch (UnknownPeriodException $e) {
            $this->logger->error(
                "Error calculating next payment date for subscription {$subscription->getId()}: {$e->getMessage()}"
            );

            return;
        }

        // Сохраняем изменения в базе данных.
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
        $this->logger->info("Processed subscription {$subscription->getId()} successfully.");
    }// end processSubscription()

    /**
     * Рассчитывает следующую дату платежа на основе текущей даты платежа
     * и периода подписки (месяц или год).
     *
     * @param Subscription $subscription Подписка, для которой вычисляется дата.
     *
     * @return DateTime Новая дата следующего платежа.
     *
     * @throws UnknownPeriodException Если период подписки не поддерживается.
     */
    private function calculateNextPaymentDate(Subscription $subscription): DateTime
    {
        $currentDate = clone $subscription->getNextPaymentDate();
        switch ($subscription->getPeriod()) {
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
    }// end calculateNextPaymentDate()
}// end class
