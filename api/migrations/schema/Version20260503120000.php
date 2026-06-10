<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260503120000 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add randomlyGenerated flag to camp table for identifying generated data';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE camp ADD randomlyGenerated BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('CREATE INDEX IDX_C1944230BBF3963D ON camp (randomlyGenerated)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE camp DROP randomlyGenerated');
        $this->addSql('DROP INDEX IDX_C1944230BBF3963D');
    }
}
