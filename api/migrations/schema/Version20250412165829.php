<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412165829 extends AbstractMigration {
    public function getDescription(): string {
        return 'MaterialItem.MaterialList is nullable';
    }

    public function up(Schema $schema): void {
        // Add Column CampId
        $this->addSql(<<<'SQL'
            ALTER TABLE material_item ADD campId VARCHAR(16) NULL
        SQL);

        // Insert values (MaterialItem -> Period -> Camp)
        $this->addSql(<<<'SQL'
            UPDATE  material_item mi
            SET     campId = (SELECT p.campId FROM period p WHERE p.id = mi.periodId)
            WHERE   mi.campId is null
            AND     mi.periodId is not null
        SQL);

        // Insert values (MaterialItem -> ContentNode -> Activity -> Camp)
        $this->addSql(<<<'SQL'
            UPDATE  material_item mi
            SET     campId = (
                        SELECT a.campId
                        FROM content_node cn
                        JOIN activity a on a.rootContentNodeId = cn.rootId
                        WHERE cn.id = mi.materialNodeId
                    )
            WHERE   mi.campId is null
            AND     mi.materialNodeId is not null;
        SQL);

        // Change Column CampId to NOT NULLABLE
        $this->addSql(<<<'SQL'
            ALTER TABLE material_item ALTER campId SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE material_item ADD CONSTRAINT FK_4B73482B6D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4B73482B6D299429 ON material_item (campId)
        SQL);

        // Drop NOT NULL Constraint
        $this->addSql(<<<'SQL'
            ALTER TABLE material_item ALTER materialListId DROP NOT NULL
        SQL);
        // Delete materialListId if it does not belong to the same camp
        $this->addSql(<<<'SQL'
            UPDATE 	material_item mi
            SET		materialListId = null
            WHERE	NOT EXISTS (
                        SELECT	1
                        FROM	material_list ml
                        WHERE	ml.id = mi.materialListId
                        AND		ml.campId = mi.campId
                    )
        SQL);
    }

    public function down(Schema $schema): void {
        $this->addSql(<<<'SQL'
            ALTER TABLE material_item ALTER materiallistid SET NOT NULL
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE material_item DROP CONSTRAINT FK_4B73482B6D299429
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4B73482B6D299429
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE material_item DROP campId
        SQL);
    }
}
