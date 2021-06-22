<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210621210442 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_type_singletext ADD content_node_id VARCHAR(16) NOT NULL');
        $this->addSql('ALTER TABLE content_type_singletext ADD CONSTRAINT FK_2DB8C52599054664 FOREIGN KEY (content_node_id) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2DB8C52599054664 ON content_type_singletext (content_node_id)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE content_type_singletext DROP CONSTRAINT FK_2DB8C52599054664');
        $this->addSql('DROP INDEX IDX_2DB8C52599054664');
        $this->addSql('ALTER TABLE content_type_singletext DROP content_node_id');
    }
}
