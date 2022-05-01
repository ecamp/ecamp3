<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220501101420 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // add rootContentNodeId on activity/category
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT fk_ac74095abf396750');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT fk_64c19c1bf396750');
        $this->addSql('ALTER TABLE activity ADD rootContentNodeId VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD rootContentNodeId VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD createTime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD updateTime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD createTime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD updateTime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

        // migrate date from abstract_content_node_owner to category/activity table
        $this->addSql('UPDATE category SET rootcontentnodeid = abstract.rootcontentnodeid, createTime = abstract.createTime, updateTime = abstract.updateTime  FROM abstract_content_node_owner abstract WHERE category.id = abstract.id');
        $this->addSql('UPDATE activity SET rootcontentnodeid = abstract.rootcontentnodeid, createTime = abstract.createTime, updateTime = abstract.updateTime FROM abstract_content_node_owner abstract WHERE activity.id = abstract.id');

        // add indexes + foreign keys
        $this->addSql('ALTER TABLE activity ALTER COLUMN rootContentNodeId SET NOT NULL');
        $this->addSql('ALTER TABLE activity ALTER COLUMN createTime SET NOT NULL');
        $this->addSql('ALTER TABLE activity ALTER COLUMN updateTime SET NOT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AF886581C FOREIGN KEY (rootContentNodeId) REFERENCES content_node_columnlayout (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC74095AF886581C ON activity (rootContentNodeId)');
        $this->addSql('CREATE INDEX IDX_AC74095A9D468A55 ON activity (createTime)');
        $this->addSql('CREATE INDEX IDX_AC74095A55AA53E2 ON activity (updateTime)');

        $this->addSql('ALTER TABLE category ALTER COLUMN rootContentNodeId SET NOT NULL');
        $this->addSql('ALTER TABLE category ALTER COLUMN createTime SET NOT NULL');
        $this->addSql('ALTER TABLE category ALTER COLUMN updateTime SET NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1F886581C FOREIGN KEY (rootContentNodeId) REFERENCES content_node_columnlayout (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1F886581C ON category (rootContentNodeId)');
        $this->addSql('CREATE INDEX IDX_64C19C19D468A55 ON category (createTime)');
        $this->addSql('CREATE INDEX IDX_64C19C155AA53E2 ON category (updateTime)');

        // remove abstract_content_node_owner
        $this->addSql('DROP TABLE abstract_content_node_owner');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE abstract_content_node_owner (id VARCHAR(16) NOT NULL, rootcontentnodeid VARCHAR(16) NOT NULL, createtime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, entitytype VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8e710ab49d468a55 ON abstract_content_node_owner (createtime)');
        $this->addSql('CREATE INDEX idx_8e710ab455aa53e2 ON abstract_content_node_owner (updatetime)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8e710ab4f886581c ON abstract_content_node_owner (rootcontentnodeid)');
        $this->addSql('ALTER TABLE abstract_content_node_owner ADD CONSTRAINT fk_8e710ab4f886581c FOREIGN KEY (rootcontentnodeid) REFERENCES content_node_columnlayout (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095AF886581C');
        $this->addSql('DROP INDEX UNIQ_AC74095AF886581C');
        $this->addSql('DROP INDEX IDX_AC74095A9D468A55');
        $this->addSql('DROP INDEX IDX_AC74095A55AA53E2');
        $this->addSql('ALTER TABLE activity DROP createTime');
        $this->addSql('ALTER TABLE activity DROP updateTime');
        $this->addSql('ALTER TABLE activity DROP rootContentNodeId');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT fk_ac74095abf396750 FOREIGN KEY (id) REFERENCES abstract_content_node_owner (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1F886581C');
        $this->addSql('DROP INDEX UNIQ_64C19C1F886581C');
        $this->addSql('DROP INDEX IDX_64C19C19D468A55');
        $this->addSql('DROP INDEX IDX_64C19C155AA53E2');
        $this->addSql('ALTER TABLE category DROP createTime');
        $this->addSql('ALTER TABLE category DROP updateTime');
        $this->addSql('ALTER TABLE category DROP rootContentNodeId');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT fk_64c19c1bf396750 FOREIGN KEY (id) REFERENCES abstract_content_node_owner (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
