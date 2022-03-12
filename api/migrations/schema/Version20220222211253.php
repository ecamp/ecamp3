<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220222211253 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule_entry RENAME COLUMN periodOffset TO startOffset');
        $this->addSql('ALTER TABLE schedule_entry RENAME COLUMN length TO endOffset');
        $this->addSql('UPDATE schedule_entry SET endOffset = endOffset + startOffset');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule_entry RENAME COLUMN startOffset TO periodOffset');
        $this->addSql('ALTER TABLE schedule_entry RENAME COLUMN endOffset TO length');
        $this->addSql('UPDATE schedule_entry SET length = length - periodOffset');
    }
}
