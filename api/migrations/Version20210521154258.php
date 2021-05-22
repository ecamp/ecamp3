<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210521154258 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE camp (id VARCHAR(16) NOT NULL, creator_id VARCHAR(16) NOT NULL, owner_id VARCHAR(16) NOT NULL, name VARCHAR(32) NOT NULL, title TEXT NOT NULL, motto TEXT DEFAULT NULL, address_name TEXT DEFAULT NULL, address_street TEXT DEFAULT NULL, address_zipcode TEXT DEFAULT NULL, address_city TEXT DEFAULT NULL, is_prototype BOOLEAN NOT NULL, camp_prototype_id VARCHAR(16) DEFAULT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C194423061220EA6 ON camp (creator_id)');
        $this->addSql('CREATE INDEX IDX_C19442307E3C61F9 ON camp (owner_id)');
        $this->addSql('CREATE INDEX IDX_C1944230EE35052C ON camp (create_time)');
        $this->addSql('CREATE INDEX IDX_C1944230BBF8CFDA ON camp (update_time)');
        $this->addSql('ALTER TABLE camp ADD CONSTRAINT FK_C194423061220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE camp ADD CONSTRAINT FK_C19442307E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE camp');
    }
}
