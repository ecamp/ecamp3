<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220416125104 extends AbstractMigration {
    public function getDescription(): string {
        return 'add indexes for searchable properties';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX IDX_C19442309AF2EB23 ON camp (isPrototype)');
        $this->addSql('CREATE INDEX IDX_C93898A7B00651C ON camp_collaboration (status)');
        $this->addSql('CREATE INDEX IDX_D7785D2CA1CB398B ON schedule_entry (startOffset)');
        $this->addSql('CREATE INDEX IDX_D7785D2CA501647F ON schedule_entry (endOffset)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_D7785D2CA1CB398B');
        $this->addSql('DROP INDEX IDX_D7785D2CA501647F');
        $this->addSql('DROP INDEX IDX_C19442309AF2EB23');
        $this->addSql('DROP INDEX IDX_C93898A7B00651C');
    }
}
