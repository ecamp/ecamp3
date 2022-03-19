<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220319210543 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX invitekey_unique');
        $this->addSql('ALTER TABLE camp_collaboration RENAME COLUMN invitekey TO inviteKeyHash');
        $this->addSql('CREATE UNIQUE INDEX inviteKeyHash_unique ON camp_collaboration (inviteKeyHash)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX inviteKeyHash_unique');
        $this->addSql('ALTER TABLE camp_collaboration RENAME COLUMN inviteKeyHash TO invitekey');
        $this->addSql('CREATE UNIQUE INDEX invitekey_unique ON camp_collaboration (invitekey)');
    }
}
