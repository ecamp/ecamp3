<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260506181000 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add persisted hasChecklists flag on camps';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE camp ADD hasChecklists BOOLEAN DEFAULT false NOT NULL');
        $this->addSql(<<<'SQL'
            UPDATE camp
            SET hasChecklists = true
            WHERE EXISTS (
                SELECT 1
                FROM checklist
                WHERE checklist.campId = camp.id
                  AND checklist.isPrototype = false
            )
        SQL);
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE camp DROP hasChecklists');
    }
}
