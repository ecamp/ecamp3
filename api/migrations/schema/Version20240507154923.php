<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507154923 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add color and abbreviation to camp_collaboration';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE camp_collaboration ADD color VARCHAR(8) DEFAULT NULL');
        $this->addSql('ALTER TABLE camp_collaboration ADD abbreviation TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE camp_collaboration DROP abbreviation');
        $this->addSql('ALTER TABLE camp_collaboration DROP color');
    }
}
