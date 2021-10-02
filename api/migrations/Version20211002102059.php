<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002102059 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abstract_content_node_owner (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rootContentNodeId VARCHAR(16) NOT NULL, entityType VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E710AB4F886581C ON abstract_content_node_owner (rootContentNodeId)');
        $this->addSql('CREATE INDEX IDX_8E710AB49D468A55 ON abstract_content_node_owner (createTime)');
        $this->addSql('CREATE INDEX IDX_8E710AB455AA53E2 ON abstract_content_node_owner (updateTime)');
        $this->addSql('CREATE TABLE activity (id VARCHAR(16) NOT NULL, title TEXT NOT NULL, location TEXT NOT NULL, campId VARCHAR(16) NOT NULL, categoryId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC74095A6D299429 ON activity (campId)');
        $this->addSql('CREATE INDEX IDX_AC74095A9C370B71 ON activity (categoryId)');
        $this->addSql('CREATE TABLE activity_responsible (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, activityId VARCHAR(16) NOT NULL, campCollaborationId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_99684FE41335E2FC ON activity_responsible (activityId)');
        $this->addSql('CREATE INDEX IDX_99684FE456778C5C ON activity_responsible (campCollaborationId)');
        $this->addSql('CREATE INDEX IDX_99684FE49D468A55 ON activity_responsible (createTime)');
        $this->addSql('CREATE INDEX IDX_99684FE455AA53E2 ON activity_responsible (updateTime)');
        $this->addSql('CREATE UNIQUE INDEX activity_campCollaboration_unique ON activity_responsible (activityId, campCollaborationId)');
        $this->addSql('CREATE TABLE camp (id VARCHAR(16) NOT NULL, campPrototypeId VARCHAR(16) DEFAULT NULL, isPrototype BOOLEAN NOT NULL, name VARCHAR(32) NOT NULL, title TEXT NOT NULL, motto TEXT DEFAULT NULL, addressName TEXT DEFAULT NULL, addressStreet TEXT DEFAULT NULL, addressZipcode TEXT DEFAULT NULL, addressCity TEXT DEFAULT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, creatorId VARCHAR(16) NOT NULL, ownerId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C194423024B2CCF6 ON camp (creatorId)');
        $this->addSql('CREATE INDEX IDX_C1944230E05EFD25 ON camp (ownerId)');
        $this->addSql('CREATE INDEX IDX_C19442309D468A55 ON camp (createTime)');
        $this->addSql('CREATE INDEX IDX_C194423055AA53E2 ON camp (updateTime)');
        $this->addSql('CREATE TABLE camp_collaboration (id VARCHAR(16) NOT NULL, inviteEmail TEXT DEFAULT NULL, inviteKey VARCHAR(64) DEFAULT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(16) NOT NULL, collaborationAcceptedBy TEXT DEFAULT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, userId VARCHAR(16) DEFAULT NULL, campId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C93898A64B64DCC ON camp_collaboration (userId)');
        $this->addSql('CREATE INDEX IDX_C93898A6D299429 ON camp_collaboration (campId)');
        $this->addSql('CREATE INDEX IDX_C93898A9D468A55 ON camp_collaboration (createTime)');
        $this->addSql('CREATE INDEX IDX_C93898A55AA53E2 ON camp_collaboration (updateTime)');
        $this->addSql('CREATE UNIQUE INDEX inviteKey_unique ON camp_collaboration (inviteKey)');
        $this->addSql('CREATE TABLE category (id VARCHAR(16) NOT NULL, categoryPrototypeId VARCHAR(16) DEFAULT NULL, short TEXT NOT NULL, name TEXT NOT NULL, color VARCHAR(8) NOT NULL, numberingStyle VARCHAR(1) NOT NULL, campId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C16D299429 ON category (campId)');
        $this->addSql('CREATE TABLE category_contenttype (category_id VARCHAR(16) NOT NULL, contenttype_id VARCHAR(16) NOT NULL, PRIMARY KEY(category_id, contenttype_id))');
        $this->addSql('CREATE INDEX IDX_C3C55A4D12469DE2 ON category_contenttype (category_id)');
        $this->addSql('CREATE INDEX IDX_C3C55A4D54491BBC ON category_contenttype (contenttype_id)');
        $this->addSql('CREATE TABLE content_node (id VARCHAR(16) NOT NULL, slot TEXT DEFAULT NULL, position INT DEFAULT NULL, jsonConfig JSON DEFAULT NULL, instanceName TEXT DEFAULT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rootId VARCHAR(16) DEFAULT NULL, parentId VARCHAR(16) DEFAULT NULL, contentTypeId VARCHAR(16) NOT NULL, strategy VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_481D0580B7939B21 ON content_node (rootId)');
        $this->addSql('CREATE INDEX IDX_481D058010EE4CEE ON content_node (parentId)');
        $this->addSql('CREATE INDEX IDX_481D0580FDA329E8 ON content_node (contentTypeId)');
        $this->addSql('CREATE INDEX IDX_481D05809D468A55 ON content_node (createTime)');
        $this->addSql('CREATE INDEX IDX_481D058055AA53E2 ON content_node (updateTime)');
        $this->addSql('CREATE TABLE content_node_columnlayout (id VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_materialnode (id VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_multiselect (id VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_multiselect_option (id VARCHAR(16) NOT NULL, translateKey TEXT NOT NULL, checked BOOLEAN NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pos INT NOT NULL, multiSelectId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FCE40C9C922275B3 ON content_node_multiselect_option (multiSelectId)');
        $this->addSql('CREATE INDEX IDX_FCE40C9C9D468A55 ON content_node_multiselect_option (createTime)');
        $this->addSql('CREATE INDEX IDX_FCE40C9C55AA53E2 ON content_node_multiselect_option (updateTime)');
        $this->addSql('CREATE TABLE content_node_singletext (id VARCHAR(16) NOT NULL, text TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_storyboard (id VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_storyboard_section (id VARCHAR(16) NOT NULL, column1 TEXT DEFAULT NULL, column2 TEXT DEFAULT NULL, column3 TEXT DEFAULT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pos INT NOT NULL, storyboardId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E2B71B9F8F096E57 ON content_node_storyboard_section (storyboardId)');
        $this->addSql('CREATE INDEX IDX_E2B71B9F9D468A55 ON content_node_storyboard_section (createTime)');
        $this->addSql('CREATE INDEX IDX_E2B71B9F55AA53E2 ON content_node_storyboard_section (updateTime)');
        $this->addSql('CREATE TABLE content_type (id VARCHAR(16) NOT NULL, name VARCHAR(32) NOT NULL, active BOOLEAN NOT NULL, entityClass TEXT NOT NULL, jsonConfig JSON DEFAULT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41BCBAEC5E237E06 ON content_type (name)');
        $this->addSql('CREATE INDEX IDX_41BCBAEC9D468A55 ON content_type (createTime)');
        $this->addSql('CREATE INDEX IDX_41BCBAEC55AA53E2 ON content_type (updateTime)');
        $this->addSql('CREATE TABLE day (id VARCHAR(16) NOT NULL, dayOffset INT NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, periodId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E5A0299085CB41E9 ON day (periodId)');
        $this->addSql('CREATE INDEX IDX_E5A029909D468A55 ON day (createTime)');
        $this->addSql('CREATE INDEX IDX_E5A0299055AA53E2 ON day (updateTime)');
        $this->addSql('CREATE UNIQUE INDEX offset_period_idx ON day (periodId, dayOffset)');
        $this->addSql('CREATE TABLE day_responsible (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, dayId VARCHAR(16) NOT NULL, campCollaborationId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_81AC6A2D194EB424 ON day_responsible (dayId)');
        $this->addSql('CREATE INDEX IDX_81AC6A2D56778C5C ON day_responsible (campCollaborationId)');
        $this->addSql('CREATE INDEX IDX_81AC6A2D9D468A55 ON day_responsible (createTime)');
        $this->addSql('CREATE INDEX IDX_81AC6A2D55AA53E2 ON day_responsible (updateTime)');
        $this->addSql('CREATE UNIQUE INDEX day_campCollaboration_unique ON day_responsible (dayId, campCollaborationId)');
        $this->addSql('CREATE TABLE material_item (id VARCHAR(16) NOT NULL, article TEXT NOT NULL, quantity DOUBLE PRECISION DEFAULT NULL, unit TEXT DEFAULT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, materialListId VARCHAR(16) NOT NULL, periodId VARCHAR(16) DEFAULT NULL, materialNodeId VARCHAR(16) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4B73482BC0B3A0A9 ON material_item (materialListId)');
        $this->addSql('CREATE INDEX IDX_4B73482B85CB41E9 ON material_item (periodId)');
        $this->addSql('CREATE INDEX IDX_4B73482BD2115263 ON material_item (materialNodeId)');
        $this->addSql('CREATE INDEX IDX_4B73482B9D468A55 ON material_item (createTime)');
        $this->addSql('CREATE INDEX IDX_4B73482B55AA53E2 ON material_item (updateTime)');
        $this->addSql('CREATE TABLE material_list (id VARCHAR(16) NOT NULL, materialListPrototypeId VARCHAR(16) DEFAULT NULL, name TEXT NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, campId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_10A0952D6D299429 ON material_list (campId)');
        $this->addSql('CREATE INDEX IDX_10A0952D9D468A55 ON material_list (createTime)');
        $this->addSql('CREATE INDEX IDX_10A0952D55AA53E2 ON material_list (updateTime)');
        $this->addSql('CREATE TABLE period (id VARCHAR(16) NOT NULL, description TEXT DEFAULT NULL, start DATE NOT NULL, "end" DATE NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, campId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C5B81ECE6D299429 ON period (campId)');
        $this->addSql('CREATE INDEX IDX_C5B81ECE9D468A55 ON period (createTime)');
        $this->addSql('CREATE INDEX IDX_C5B81ECE55AA53E2 ON period (updateTime)');
        $this->addSql('CREATE TABLE schedule_entry (id VARCHAR(16) NOT NULL, periodOffset INT NOT NULL, length INT NOT NULL, "left" DOUBLE PRECISION DEFAULT NULL, width DOUBLE PRECISION DEFAULT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, periodId VARCHAR(16) NOT NULL, activityId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D7785D2C85CB41E9 ON schedule_entry (periodId)');
        $this->addSql('CREATE INDEX IDX_D7785D2C1335E2FC ON schedule_entry (activityId)');
        $this->addSql('CREATE INDEX IDX_D7785D2C9D468A55 ON schedule_entry (createTime)');
        $this->addSql('CREATE INDEX IDX_D7785D2C55AA53E2 ON schedule_entry (updateTime)');
        $this->addSql('CREATE TABLE "user" (id VARCHAR(16) NOT NULL, email VARCHAR(64) NOT NULL, username VARCHAR(32) NOT NULL, firstname TEXT DEFAULT NULL, surname TEXT DEFAULT NULL, nickname TEXT DEFAULT NULL, language VARCHAR(20) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, activationKeyHash VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE INDEX IDX_8D93D6499D468A55 ON "user" (createTime)');
        $this->addSql('CREATE INDEX IDX_8D93D64955AA53E2 ON "user" (updateTime)');
        $this->addSql('ALTER TABLE abstract_content_node_owner ADD CONSTRAINT FK_8E710AB4F886581C FOREIGN KEY (rootContentNodeId) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A6D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A9C370B71 FOREIGN KEY (categoryId) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095ABF396750 FOREIGN KEY (id) REFERENCES abstract_content_node_owner (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity_responsible ADD CONSTRAINT FK_99684FE41335E2FC FOREIGN KEY (activityId) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity_responsible ADD CONSTRAINT FK_99684FE456778C5C FOREIGN KEY (campCollaborationId) REFERENCES camp_collaboration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE camp ADD CONSTRAINT FK_C194423024B2CCF6 FOREIGN KEY (creatorId) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE camp ADD CONSTRAINT FK_C1944230E05EFD25 FOREIGN KEY (ownerId) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE camp_collaboration ADD CONSTRAINT FK_C93898A64B64DCC FOREIGN KEY (userId) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE camp_collaboration ADD CONSTRAINT FK_C93898A6D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C16D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1BF396750 FOREIGN KEY (id) REFERENCES abstract_content_node_owner (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_contenttype ADD CONSTRAINT FK_C3C55A4D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_contenttype ADD CONSTRAINT FK_C3C55A4D54491BBC FOREIGN KEY (contenttype_id) REFERENCES content_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D0580B7939B21 FOREIGN KEY (rootId) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D058010EE4CEE FOREIGN KEY (parentId) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D0580FDA329E8 FOREIGN KEY (contentTypeId) REFERENCES content_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_columnlayout ADD CONSTRAINT FK_7D8CEA8BF396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_materialnode ADD CONSTRAINT FK_F4B3137FBF396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_multiselect ADD CONSTRAINT FK_6EC3519BBF396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_multiselect_option ADD CONSTRAINT FK_FCE40C9C922275B3 FOREIGN KEY (multiSelectId) REFERENCES content_node_multiselect (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_singletext ADD CONSTRAINT FK_E41B9CEABF396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_storyboard ADD CONSTRAINT FK_C6AA45AFBF396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_storyboard_section ADD CONSTRAINT FK_E2B71B9F8F096E57 FOREIGN KEY (storyboardId) REFERENCES content_node_storyboard (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE day ADD CONSTRAINT FK_E5A0299085CB41E9 FOREIGN KEY (periodId) REFERENCES period (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE day_responsible ADD CONSTRAINT FK_81AC6A2D194EB424 FOREIGN KEY (dayId) REFERENCES day (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE day_responsible ADD CONSTRAINT FK_81AC6A2D56778C5C FOREIGN KEY (campCollaborationId) REFERENCES camp_collaboration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE material_item ADD CONSTRAINT FK_4B73482BC0B3A0A9 FOREIGN KEY (materialListId) REFERENCES material_list (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE material_item ADD CONSTRAINT FK_4B73482B85CB41E9 FOREIGN KEY (periodId) REFERENCES period (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE material_item ADD CONSTRAINT FK_4B73482BD2115263 FOREIGN KEY (materialNodeId) REFERENCES content_node_materialnode (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE material_list ADD CONSTRAINT FK_10A0952D6D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE period ADD CONSTRAINT FK_C5B81ECE6D299429 FOREIGN KEY (campId) REFERENCES camp (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE schedule_entry ADD CONSTRAINT FK_D7785D2C85CB41E9 FOREIGN KEY (periodId) REFERENCES period (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE schedule_entry ADD CONSTRAINT FK_D7785D2C1335E2FC FOREIGN KEY (activityId) REFERENCES activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095ABF396750');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1BF396750');
        $this->addSql('ALTER TABLE activity_responsible DROP CONSTRAINT FK_99684FE41335E2FC');
        $this->addSql('ALTER TABLE schedule_entry DROP CONSTRAINT FK_D7785D2C1335E2FC');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095A6D299429');
        $this->addSql('ALTER TABLE camp_collaboration DROP CONSTRAINT FK_C93898A6D299429');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C16D299429');
        $this->addSql('ALTER TABLE material_list DROP CONSTRAINT FK_10A0952D6D299429');
        $this->addSql('ALTER TABLE period DROP CONSTRAINT FK_C5B81ECE6D299429');
        $this->addSql('ALTER TABLE activity_responsible DROP CONSTRAINT FK_99684FE456778C5C');
        $this->addSql('ALTER TABLE day_responsible DROP CONSTRAINT FK_81AC6A2D56778C5C');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095A9C370B71');
        $this->addSql('ALTER TABLE category_contenttype DROP CONSTRAINT FK_C3C55A4D12469DE2');
        $this->addSql('ALTER TABLE abstract_content_node_owner DROP CONSTRAINT FK_8E710AB4F886581C');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D0580B7939B21');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D058010EE4CEE');
        $this->addSql('ALTER TABLE content_node_columnlayout DROP CONSTRAINT FK_7D8CEA8BF396750');
        $this->addSql('ALTER TABLE content_node_materialnode DROP CONSTRAINT FK_F4B3137FBF396750');
        $this->addSql('ALTER TABLE content_node_multiselect DROP CONSTRAINT FK_6EC3519BBF396750');
        $this->addSql('ALTER TABLE content_node_singletext DROP CONSTRAINT FK_E41B9CEABF396750');
        $this->addSql('ALTER TABLE content_node_storyboard DROP CONSTRAINT FK_C6AA45AFBF396750');
        $this->addSql('ALTER TABLE material_item DROP CONSTRAINT FK_4B73482BD2115263');
        $this->addSql('ALTER TABLE content_node_multiselect_option DROP CONSTRAINT FK_FCE40C9C922275B3');
        $this->addSql('ALTER TABLE content_node_storyboard_section DROP CONSTRAINT FK_E2B71B9F8F096E57');
        $this->addSql('ALTER TABLE category_contenttype DROP CONSTRAINT FK_C3C55A4D54491BBC');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D0580FDA329E8');
        $this->addSql('ALTER TABLE day_responsible DROP CONSTRAINT FK_81AC6A2D194EB424');
        $this->addSql('ALTER TABLE material_item DROP CONSTRAINT FK_4B73482BC0B3A0A9');
        $this->addSql('ALTER TABLE day DROP CONSTRAINT FK_E5A0299085CB41E9');
        $this->addSql('ALTER TABLE material_item DROP CONSTRAINT FK_4B73482B85CB41E9');
        $this->addSql('ALTER TABLE schedule_entry DROP CONSTRAINT FK_D7785D2C85CB41E9');
        $this->addSql('ALTER TABLE camp DROP CONSTRAINT FK_C194423024B2CCF6');
        $this->addSql('ALTER TABLE camp DROP CONSTRAINT FK_C1944230E05EFD25');
        $this->addSql('ALTER TABLE camp_collaboration DROP CONSTRAINT FK_C93898A64B64DCC');
        $this->addSql('DROP TABLE abstract_content_node_owner');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_responsible');
        $this->addSql('DROP TABLE camp');
        $this->addSql('DROP TABLE camp_collaboration');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_contenttype');
        $this->addSql('DROP TABLE content_node');
        $this->addSql('DROP TABLE content_node_columnlayout');
        $this->addSql('DROP TABLE content_node_materialnode');
        $this->addSql('DROP TABLE content_node_multiselect');
        $this->addSql('DROP TABLE content_node_multiselect_option');
        $this->addSql('DROP TABLE content_node_singletext');
        $this->addSql('DROP TABLE content_node_storyboard');
        $this->addSql('DROP TABLE content_node_storyboard_section');
        $this->addSql('DROP TABLE content_type');
        $this->addSql('DROP TABLE day');
        $this->addSql('DROP TABLE day_responsible');
        $this->addSql('DROP TABLE material_item');
        $this->addSql('DROP TABLE material_list');
        $this->addSql('DROP TABLE period');
        $this->addSql('DROP TABLE schedule_entry');
        $this->addSql('DROP TABLE "user"');
    }
}
