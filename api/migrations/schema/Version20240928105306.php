<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928105306 extends AbstractMigration {
    public function getDescription(): string {
        return 'checklistitem_checklistid_parentid_position_unique deferrable';
    }

    public function up(Schema $schema): void {
        $this->addSql(
            <<<'EOF'
                drop index checklistitem_checklistid_parentid_position_unique
                EOF
        );
        $this->addSql(
            <<<'EOF'
                alter table checklist_item
                    add constraint checklistitem_checklistid_parentid_position_unique
                        unique (checklistid, parentid, position)
                            deferrable initially deferred
            EOF
        );
    }

    public function down(Schema $schema): void {
        $this->addSql(
            <<<'EOF'
                alter table checklist_item 
                    drop constraint checklistitem_checklistid_parentid_position_unique
                EOF
        );
        $this->addSql(
            <<<'EOF'
                CREATE UNIQUE INDEX checklistitem_checklistid_parentid_position_unique 
                    ON checklist_item (checklistid, parentid, position)
                EOF
        );
    }
}
