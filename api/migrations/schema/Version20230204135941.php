<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204135941 extends AbstractMigration {
    public function getDescription(): string {
        return 'Make unique constraint offset_period_idx a deferred constraint';
    }

    public function up(Schema $schema): void {
        $this->addSql('DROP INDEX offset_period_idx');
        $this->addSql(
            <<<'EOF'
                        alter table day 
                            add constraint offset_period_idx 
                                unique (periodId, dayOffset)
                                    deferrable initially deferred
                        EOF
        );
    }

    public function down(Schema $schema): void {
        $this->addSql(
            <<<'EOF'
                        alter table day 
                            drop constraint offset_period_idx
                        EOF
        );
        $this->addSql('CREATE UNIQUE INDEX offset_period_idx ON day (periodId, dayOffset)');
    }
}
