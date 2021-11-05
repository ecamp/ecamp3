<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211030110620 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abstract_content_node_owner DROP CONSTRAINT FK_8E710AB4F886581C');
        $this->addSql('ALTER TABLE abstract_content_node_owner ADD CONSTRAINT FK_8E710AB4F886581C FOREIGN KEY (rootContentNodeId) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE abstract_content_node_owner DROP CONSTRAINT fk_8e710ab4f886581c');
        $this->addSql('ALTER TABLE abstract_content_node_owner ADD CONSTRAINT fk_8e710ab4f886581c FOREIGN KEY (rootcontentnodeid) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
