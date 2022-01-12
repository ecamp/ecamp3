<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Wraps a database transaction around all SQL statements in the whole request.
 */
final class RequestTransactionListener implements EventSubscriberInterface {
    public function __construct(private EntityManagerInterface $entityManager) {
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::CONTROLLER => ['startTransaction', 10],
            KernelEvents::RESPONSE => ['commitTransaction', 10],
            // In the case that both the Exception and Response events are triggered, we want to make sure the
            // transaction is rolled back before trying to commit it.
            KernelEvents::EXCEPTION => ['rollbackTransaction', 11],
        ];
    }

    public function startTransaction(): void {
        $this->entityManager->getConnection()->beginTransaction();
    }

    public function commitTransaction(): void {
        $this->entityManager->flush();
        $this->entityManager->getConnection()->commit();
    }

    public function rollbackTransaction(ExceptionEvent $event): void {
        $this->entityManager->getConnection()->rollBack();
        $this->entityManager->clear();
    }
}
