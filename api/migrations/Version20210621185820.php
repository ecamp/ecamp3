<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210621185820 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id VARCHAR(16) NOT NULL, camp_id VARCHAR(16) NOT NULL, title TEXT NOT NULL, location TEXT NOT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC74095A77075ABB ON activity (camp_id)');
        $this->addSql('CREATE INDEX IDX_AC74095AEE35052C ON activity (create_time)');
        $this->addSql('CREATE INDEX IDX_AC74095ABBF8CFDA ON activity (update_time)');
        $this->addSql('CREATE TABLE content_node (id VARCHAR(16) NOT NULL, owner_id VARCHAR(16) DEFAULT NULL, root_id VARCHAR(16) NULL, parent_id VARCHAR(16) DEFAULT NULL, content_type_id VARCHAR(16) NOT NULL, slot TEXT DEFAULT NULL, position INT DEFAULT NULL, json_config JSON DEFAULT NULL, instance_name TEXT DEFAULT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_481D05807E3C61F9 ON content_node (owner_id)');
        $this->addSql('CREATE INDEX IDX_481D058079066886 ON content_node (root_id)');
        $this->addSql('CREATE INDEX IDX_481D0580727ACA70 ON content_node (parent_id)');
        $this->addSql('CREATE INDEX IDX_481D05801A445520 ON content_node (content_type_id)');
        $this->addSql('CREATE INDEX IDX_481D0580EE35052C ON content_node (create_time)');
        $this->addSql('CREATE INDEX IDX_481D0580BBF8CFDA ON content_node (update_time)');
        $this->addSql('CREATE TABLE content_type (id VARCHAR(16) NOT NULL, name VARCHAR(32) NOT NULL, active BOOLEAN NOT NULL, strategy_class TEXT NOT NULL, json_config JSON DEFAULT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41BCBAEC5E237E06 ON content_type (name)');
        $this->addSql('CREATE INDEX IDX_41BCBAECEE35052C ON content_type (create_time)');
        $this->addSql('CREATE INDEX IDX_41BCBAECBBF8CFDA ON content_type (update_time)');
        $this->addSql('CREATE TABLE content_type_singletext (id VARCHAR(16) NOT NULL, text TEXT DEFAULT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2DB8C525EE35052C ON content_type_singletext (create_time)');
        $this->addSql('CREATE INDEX IDX_2DB8C525BBF8CFDA ON content_type_singletext (update_time)');
        $this->addSql('CREATE TABLE period (id VARCHAR(16) NOT NULL, camp_id VARCHAR(16) NOT NULL, description TEXT DEFAULT NULL, start DATE NOT NULL, "end" DATE NOT NULL, create_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C5B81ECE77075ABB ON period (camp_id)');
        $this->addSql('CREATE INDEX IDX_C5B81ECEEE35052C ON period (create_time)');
        $this->addSql('CREATE INDEX IDX_C5B81ECEBBF8CFDA ON period (update_time)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A77075ABB FOREIGN KEY (camp_id) REFERENCES camp (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D05807E3C61F9 FOREIGN KEY (owner_id) REFERENCES activity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D058079066886 FOREIGN KEY (root_id) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D0580727ACA70 FOREIGN KEY (parent_id) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D05801A445520 FOREIGN KEY (content_type_id) REFERENCES content_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE77075ABB FOREIGN KEY (camp_id) REFERENCES camp (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D05807E3C61F9');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D058079066886');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D0580727ACA70');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D05801A445520');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE content_node');
        $this->addSql('DROP TABLE content_type');
        $this->addSql('DROP TABLE content_type_singletext');
        $this->addSql('DROP TABLE period');
    }
}
