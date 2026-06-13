<?php

namespace App\Doctrine\DBAL\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Schema\SchemaManagerFactory;

final readonly class CustomSchemaManagerFactory implements SchemaManagerFactory {
    public function __construct(
        private DefaultSchemaManagerFactory $defaultFactory = new DefaultSchemaManagerFactory(),
    ) {}

    #[\Override]
    public function createSchemaManager(Connection $connection): AbstractSchemaManager {
        $platform = $connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            return new CustomPostgreSQLSchemaManager($connection, $platform);
        }

        return $this->defaultFactory->createSchemaManager($connection);
    }
}
