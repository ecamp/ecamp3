<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220417142152 extends AbstractMigration {
    public function getDescription(): string {
        return 'Fix schema migration for MaterialList';
    }

    public function up(Schema $schema): void {
        $this->addSql('DROP INDEX idx_10a0952d56778c5c');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_10A0952D56778C5C ON material_list (campCollaborationId)');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP INDEX UNIQ_10A0952D56778C5C');
        $this->addSql('CREATE INDEX idx_10a0952d56778c5c ON material_list (campcollaborationid)');
    }
}
