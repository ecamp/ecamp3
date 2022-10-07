<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220611193723 extends AbstractMigration {
    public function getDescription(): string {
        return 'ContentNode: Change inheritance type from JOINED to SINGLE_TABLE';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE content_node ADD data JSONB DEFAULT NULL');

        // migrate data for columnlayout
        $this->addSql('UPDATE content_node cn SET data = jsonb_build_object(\'columns\', cnc."columns") FROM content_node_columnlayout cnc WHERE cn.id = cnc.id');

        // migrate data for singletext
        $this->addSql('UPDATE content_node cn SET data = jsonb_build_object(\'text\', coalesce(cnt."text",\'\')) FROM content_node_singletext cnt WHERE cn.id = cnt.id');

        // migrate data for multiselect
        $this->addSql("UPDATE content_node cn
            SET data = jsonb_build_object('options', '{}'::jsonb)
            FROM content_node_multiselect cnm
            WHERE cn.id = cnm.id");

        $this->addSql("UPDATE content_node cn
            SET data = jsonb_build_object('options', multiselect.options)
            FROM
                ( 
                    SELECT ms.id, jsonb_object_agg(mso.translateKey, jsonb_build_object('checked', mso.checked)) as options
                    FROM content_node_multiselect ms
                    INNER JOIN content_node_multiselect_option mso
                    ON ms.id = mso.multiselectid
                    GROUP BY ms.id 
                ) as multiselect
            WHERE cn.id = multiselect.id");

        // migrate data for storyboard
        $this->addSql("UPDATE content_node cn
            SET data = jsonb_build_object('sections', '{}'::jsonb)
            FROM content_node_storyboard cns
            WHERE cn.id = cns.id");

        $this->addSql("UPDATE content_node cn
            SET data = jsonb_build_object('sections', storyboard.sections)
            FROM
                ( 
                    SELECT sb.id, jsonb_object_agg(gen_random_uuid(), json_build_object('column1', coalesce(sbs.column1,''), 'column2', coalesce(sbs.column2,''), 'column3', coalesce(sbs.column3,''), 'position', coalesce(sbs.position,0))) as sections
                    FROM content_node_storyboard sb
                    INNER join content_node_storyboard_section sbs
                    ON sb.id=sbs.storyboardid
                    GROUP BY sb.id 
                ) as storyboard
            WHERE cn.id = storyboard.id");

        // remove legacy tables and update foreign keys
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT fk_481d0580b7939b21');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT fk_ac74095af886581c');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT fk_64c19c1f886581c');
        $this->addSql('ALTER TABLE material_item DROP CONSTRAINT fk_4b73482bd2115263');
        $this->addSql('ALTER TABLE content_node_multiselect_option DROP CONSTRAINT fk_fce40c9c922275b3');
        $this->addSql('ALTER TABLE content_node_storyboard_section DROP CONSTRAINT fk_e2b71b9f8f096e57');
        $this->addSql('DROP TABLE content_node_columnlayout');
        $this->addSql('DROP TABLE content_node_materialnode');
        $this->addSql('DROP TABLE content_node_multiselect');
        $this->addSql('DROP TABLE content_node_singletext');
        $this->addSql('DROP TABLE content_node_storyboard');
        $this->addSql('DROP TABLE content_node_multiselect_option');
        $this->addSql('DROP TABLE content_node_storyboard_section');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AF886581C FOREIGN KEY (rootContentNodeId) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1F886581C FOREIGN KEY (rootContentNodeId) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D0580B7939B21 FOREIGN KEY (rootId) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE material_item ADD CONSTRAINT FK_4B73482BD2115263 FOREIGN KEY (materialNodeId) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql('CREATE TABLE content_node_columnlayout (id VARCHAR(16) NOT NULL, columns JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_materialnode (id VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_multiselect (id VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_singletext (id VARCHAR(16) NOT NULL, text TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_storyboard (id VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE content_node_multiselect_option (id VARCHAR(16) NOT NULL, multiselectid VARCHAR(16) NOT NULL, translatekey TEXT NOT NULL, checked BOOLEAN NOT NULL, createtime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "position" INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fce40c9c55aa53e2 ON content_node_multiselect_option (updatetime)');
        $this->addSql('CREATE INDEX idx_fce40c9c922275b3 ON content_node_multiselect_option (multiselectid)');
        $this->addSql('CREATE INDEX idx_fce40c9c9d468a55 ON content_node_multiselect_option (createtime)');
        $this->addSql('CREATE TABLE content_node_storyboard_section (id VARCHAR(16) NOT NULL, storyboardid VARCHAR(16) NOT NULL, column1 TEXT DEFAULT NULL, column2 TEXT DEFAULT NULL, column3 TEXT DEFAULT NULL, createtime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "position" INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_e2b71b9f9d468a55 ON content_node_storyboard_section (createtime)');
        $this->addSql('CREATE INDEX idx_e2b71b9f55aa53e2 ON content_node_storyboard_section (updatetime)');
        $this->addSql('CREATE INDEX idx_e2b71b9f8f096e57 ON content_node_storyboard_section (storyboardid)');
        $this->addSql('ALTER TABLE content_node_columnlayout ADD CONSTRAINT fk_7d8cea8bf396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_materialnode ADD CONSTRAINT fk_f4b3137fbf396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_multiselect ADD CONSTRAINT fk_6ec3519bbf396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_singletext ADD CONSTRAINT fk_e41b9ceabf396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_storyboard ADD CONSTRAINT fk_c6aa45afbf396750 FOREIGN KEY (id) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_multiselect_option ADD CONSTRAINT fk_fce40c9c922275b3 FOREIGN KEY (multiselectid) REFERENCES content_node_multiselect (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node_storyboard_section ADD CONSTRAINT fk_e2b71b9f8f096e57 FOREIGN KEY (storyboardid) REFERENCES content_node_storyboard (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT fk_481d0580b7939b21');
        $this->addSql('ALTER TABLE content_node DROP data');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT fk_481d0580b7939b21 FOREIGN KEY (rootid) REFERENCES content_node_columnlayout (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE material_item DROP CONSTRAINT fk_4b73482bd2115263');
        $this->addSql('ALTER TABLE material_item ADD CONSTRAINT fk_4b73482bd2115263 FOREIGN KEY (materialnodeid) REFERENCES content_node_materialnode (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT fk_ac74095af886581c');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT fk_ac74095af886581c FOREIGN KEY (rootcontentnodeid) REFERENCES content_node_columnlayout (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT fk_64c19c1f886581c');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT fk_64c19c1f886581c FOREIGN KEY (rootcontentnodeid) REFERENCES content_node_columnlayout (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
