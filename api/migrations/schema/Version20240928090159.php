<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

require_once __DIR__.'/checklists/helpers.php';

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928090159 extends AbstractMigration {
    public function getDescription(): string {
        return 'Insert PBS-Checklist';
    }

    public function up(Schema $schema): void {
        $statements = getStatementsForMigrationFile(__DIR__.'/checklists/pbs-checklist.sql');
        foreach ($statements as $statement) {
            if (trim($statement)) {
                $this->addSql($statement);
            }
        }
    }

    public function down(Schema $schema): void {}
}
