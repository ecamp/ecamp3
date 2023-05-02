<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502191133 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add unique constraint on activity_progress_label (campid, position)';
    }

    public function up(Schema $schema): void {
        $this->addSql(
            <<<'EOF'
                alter table activity_progress_label
                    add constraint activity_progress_label_unique
                        unique (campid, position)
                            deferrable initially deferred
            EOF
        );
    }

    public function down(Schema $schema): void {
        $this->addSql(
            <<<'EOF'
                        alter table activity_progress_label 
                            drop constraint activity_progress_label_unique
                        EOF
        );
    }
}
