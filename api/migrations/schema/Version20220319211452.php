<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220319211452 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add campCollaboration to MaterialList';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE material_list ADD campCollaborationId VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE material_list ADD CONSTRAINT FK_10A0952D56778C5C FOREIGN KEY (campCollaborationId) REFERENCES camp_collaboration (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE material_list ALTER COLUMN name DROP NOT NULL');
        $this->addSql('CREATE INDEX IDX_10A0952D56778C5C ON material_list (campCollaborationId)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE material_list ALTER COLUMN name SET NOT NULL');
        $this->addSql('ALTER TABLE material_list DROP CONSTRAINT FK_10A0952D56778C5C');
        $this->addSql('DROP INDEX IDX_10A0952D56778C5C');
        $this->addSql('ALTER TABLE material_list DROP campCollaborationId');
    }
}
