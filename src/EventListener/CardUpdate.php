<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Receipt;
use App\Entity\Spend;
use App\Entity\Transfer;
use App\Service\CardService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;

#[AsDoctrineListener('prePersist')]
final class CardUpdate
{
    public function __construct(private CardService $cardService) {}//end __construct()

    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Receipt)
        {
            $this->cardService->changeBalance($entity->getCard(), $entity->getBalance());
        }
        if ($entity instanceof Spend)
        {
            $this->cardService->changeBalance($entity->getCard(), -$entity->getBalance());
        }
        if ($entity instanceof Transfer)
        {
            $balance = $entity->getBalance();

            $this->cardService->changeBalance($entity->getCardIn(), $balance);
            $this->cardService->changeBalance($entity->getCardOut(), -$balance);
        }
    }//end prePersist()
}//end class
