<?php

namespace App\EventListener;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use SplStack;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Wraps a database transaction around all SQL statements in the whole request.
 */
final class RequestTransactionListener implements EventSubscriberInterface {
    private SplStack $transactionLevelStack;

    public function __construct(private EntityManagerInterface $entityManager) {
        $this->transactionLevelStack = new SplStack();
    }

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    public static function getSubscribedEvents(): array {
        return [
            // Symfony Event-Call-Order is:
            //
            // Normal Request:
            // - KernelEvents::REQUEST
            //    -> Start Transaction
            // - KernelEvents::RESPONSE
            //    -> Commit
            //
            // Bad Request (with Exception):
            // - KernelEvents::REQUEST
            //    -> Start Transaction
            // - KernelEvents::EXCEPTION
            //    -> Rollback
            // - KernelEvents::RESPONSE
            //    -> Noop

            KernelEvents::REQUEST => ['startTransaction', 251],
            KernelEvents::RESPONSE => ['commitTransaction', 10],
            KernelEvents::EXCEPTION => ['rollbackTransaction', 11],
        ];
    }

    /**
     * @throws Exception
     */
    public function startTransaction(KernelEvent $event): void {
        if (HttpKernelInterface::MAIN_REQUEST != $event->getRequestType()) {
            return;
        }
        if (!$this->transactionLevelStack->isEmpty()) {
            throw new RuntimeException('startTransaction called more than once');
        }
        $this->entityManager->getConnection()->beginTransaction();
        $this->transactionLevelStack->push((object) [
            'level' => $this->entityManager->getConnection()->getTransactionNestingLevel(),
            'rollbacked' => false,
        ]);
    }

    /**
     * @throws Exception
     */
    public function commitTransaction(KernelEvent $event): void {
        if (HttpKernelInterface::MAIN_REQUEST != $event->getRequestType()) {
            return;
        }
        if ($this->transactionLevelStack->isEmpty()) {
            throw new RuntimeException('Trying to commit a transaction, when no transaction was started.');
        }

        $transactionLevel = $this->transactionLevelStack->top();
        if (!$transactionLevel->rollbacked) {
            $this->validateTransactionNestingLevel();
            $this->entityManager->getConnection()->commit();
        }

        $this->transactionLevelStack->pop();
    }

    /**
     * @throws Exception
     */
    public function rollbackTransaction(ExceptionEvent $event): void {
        if (in_array($event->getRequest()->getMethod(), ['OPTIONS', 'GET'], true)) {
            return;
        }

        try {
            if ($this->transactionLevelStack->isEmpty()) {
                throw new RuntimeException('Trying to rollback a transaction, when no transaction was started.');
            }

            $this->validateTransactionNestingLevel();
            $transactionLevel = (object) $this->transactionLevelStack->top();
            $transactionLevel->rollbacked = true;
        } finally {
            try {
                $this->entityManager->getConnection()->rollBack();
            } finally {
                $this->entityManager->clear();
            }
        }
    }

    private function validateTransactionNestingLevel(): void {
        $currentTransactionNestingLevel = $this->entityManager->getConnection()->getTransactionNestingLevel();
        $expectedTransactionNestingLevel = $this->transactionLevelStack->top()->level;
        if ($currentTransactionNestingLevel !== $expectedTransactionNestingLevel) {
            throw new RuntimeException(
                "Transaction starts and ends were not symmetric when ending a transaction, 
                expected nesting level {$expectedTransactionNestingLevel},
                was {$currentTransactionNestingLevel}"
            );
        }
    }
}
