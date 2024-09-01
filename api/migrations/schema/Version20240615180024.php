<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240615180024 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add checklist_item table';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE checklist_item (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text TEXT NOT NULL, position INT NOT NULL, checklistId VARCHAR(16) NOT NULL, parentId VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_99EB20F9BA23A13 ON checklist_item (checklistId)');
        $this->addSql('CREATE INDEX IDX_99EB20F910EE4CEE ON checklist_item (parentId)');
        $this->addSql('CREATE INDEX IDX_99EB20F99D468A55 ON checklist_item (createTime)');
        $this->addSql('CREATE INDEX IDX_99EB20F955AA53E2 ON checklist_item (updateTime)');
        $this->addSql('ALTER TABLE checklist_item ADD CONSTRAINT FK_99EB20F9BA23A13 FOREIGN KEY (checklistId) REFERENCES checklist (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checklist_item ADD CONSTRAINT FK_99EB20F910EE4CEE FOREIGN KEY (parentId) REFERENCES checklist_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE checklist_item DROP CONSTRAINT FK_99EB20F9BA23A13');
        $this->addSql('ALTER TABLE checklist_item DROP CONSTRAINT FK_99EB20F910EE4CEE');
        $this->addSql('DROP TABLE checklist_item');
    }
}
