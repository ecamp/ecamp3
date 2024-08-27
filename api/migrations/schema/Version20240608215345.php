<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240608215345 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE checklist (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name TEXT NOT NULL, campId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C696D2F6D299429 ON checklist (campId)');
        $this->addSql('CREATE INDEX IDX_5C696D2F9D468A55 ON checklist (createTime)');
        $this->addSql('CREATE INDEX IDX_5C696D2F55AA53E2 ON checklist (updateTime)');
        $this->addSql('ALTER TABLE checklist ADD CONSTRAINT FK_5C696D2F6D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE checklist DROP CONSTRAINT FK_5C696D2F6D299429');
        $this->addSql('DROP TABLE checklist');
    }
}
