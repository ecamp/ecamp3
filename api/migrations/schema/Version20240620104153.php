<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620104153 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE checklistnode_checklistitem (checklistnode_id VARCHAR(16) NOT NULL, checklistitem_id VARCHAR(16) NOT NULL, PRIMARY KEY(checklistnode_id, checklistitem_id))');
        $this->addSql('CREATE INDEX IDX_5A2B5B31DE6B6F00 ON checklistnode_checklistitem (checklistnode_id)');
        $this->addSql('CREATE INDEX IDX_5A2B5B318A09A289 ON checklistnode_checklistitem (checklistitem_id)');
        $this->addSql('ALTER TABLE checklistnode_checklistitem ADD CONSTRAINT FK_5A2B5B31DE6B6F00 FOREIGN KEY (checklistnode_id) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checklistnode_checklistitem ADD CONSTRAINT FK_5A2B5B318A09A289 FOREIGN KEY (checklistitem_id) REFERENCES checklist_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE checklistnode_checklistitem DROP CONSTRAINT FK_5A2B5B31DE6B6F00');
        $this->addSql('ALTER TABLE checklistnode_checklistitem DROP CONSTRAINT FK_5A2B5B318A09A289');
        $this->addSql('DROP TABLE checklistnode_checklistitem');
    }
}
