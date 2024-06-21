<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240621195713 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checklistnode_checklistitem DROP CONSTRAINT FK_5A2B5B31DE6B6F00');
        $this->addSql('ALTER TABLE checklistnode_checklistitem DROP CONSTRAINT FK_5A2B5B318A09A289');
        $this->addSql('ALTER TABLE checklistnode_checklistitem ADD CONSTRAINT FK_5A2B5B31DE6B6F00 FOREIGN KEY (checklistnode_id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checklistnode_checklistitem ADD CONSTRAINT FK_5A2B5B318A09A289 FOREIGN KEY (checklistitem_id) REFERENCES checklist_item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE checklistnode_checklistitem DROP CONSTRAINT fk_5a2b5b31de6b6f00');
        $this->addSql('ALTER TABLE checklistnode_checklistitem DROP CONSTRAINT fk_5a2b5b318a09a289');
        $this->addSql('ALTER TABLE checklistnode_checklistitem ADD CONSTRAINT fk_5a2b5b31de6b6f00 FOREIGN KEY (checklistnode_id) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checklistnode_checklistitem ADD CONSTRAINT fk_5a2b5b318a09a289 FOREIGN KEY (checklistitem_id) REFERENCES checklist_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
