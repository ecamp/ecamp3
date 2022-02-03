<?php

declare(strict_types=1);

namespace DataMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

require_once __DIR__.'/helpers.php';

final class Version202201230531 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        $this->connection->exec(file_get_contents(str_replace('.php', '_data.sql', __FILE__)));
    }

    public function down(Schema $schema): void {
    }
}
