<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240621060047 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add color and abbreviation to profile';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE profile ADD color VARCHAR(8) DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD abbreviation TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE profile DROP color');
        $this->addSql('ALTER TABLE profile DROP abbreviation');
    }
}
