<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210628093603 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE abstract_camp_owner');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE abstract_camp_owner (id VARCHAR(16) NOT NULL, createtime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, entitytype VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_5aefdebc9d468a55 ON abstract_camp_owner (createtime)');
        $this->addSql('CREATE INDEX idx_5aefdebc55aa53e2 ON abstract_camp_owner (updatetime)');
    }
}
