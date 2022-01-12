<?php

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;

/**
 * Enables the use of SAVEPOINT for nested transactions, in DBMS that support it (PostgreSQL).
 * This is necessary for being able to wrap a transaction around the whole request, and also
 * running the API tests with fixtures in a transaction.
 */
final class EnableSavepointsListener implements EventSubscriber {
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array {
        return [Events::postConnect];
    }

    /**
     * Fired right when the doctrine connection first starts its
     * database session.
     *
     * @param ConnectionEventArgs $event dispatched from the connection
     *
     * @throws Exception
     */
    public function postConnect(ConnectionEventArgs $event) {
        $conn = $event->getConnection();
        if ($conn->getDatabasePlatform()->supportsSavepoints()) {
            $event->getConnection()->setNestTransactionsWithSavepoints(true);
        }
    }
}
