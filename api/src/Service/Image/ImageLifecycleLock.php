<?php

namespace App\Service\Image;

use Doctrine\DBAL\Connection;

final readonly class ImageLifecycleLock {
    public function __construct(private Connection $connection) {}

    public function acquire(): void {
        if (!$this->connection->isTransactionActive()) {
            throw new \LogicException('Image lifecycle locking requires a transaction.');
        }
        $this->connection->executeQuery("SELECT pg_advisory_xact_lock(hashtext('image-lifecycle'))");
    }
}
