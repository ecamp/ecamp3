<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261024121149 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add ysPhysicalActivityDurationMinutes to schedule_entry';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE schedule_entry ADD ysPhysicalActivityDurationMinutes INT DEFAULT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE schedule_entry DROP ysPhysicalActivityDurationMinutes');
    }
}
