<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221001082012 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove username from profile';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_8157aa0ff85e0677');
        $this->addSql('ALTER TABLE profile DROP username');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "profile" ADD username VARCHAR(64) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_8157aa0ff85e0677 ON "profile" (username)');
    }
}
