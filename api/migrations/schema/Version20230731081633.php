<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731081633 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add extension pgcrypto';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pgcrypto');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP EXTENSION IF EXISTS pgcrypto');
    }
}
