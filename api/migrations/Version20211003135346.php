<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211003135346 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD googleId VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD hitobitoId VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ALTER username TYPE VARCHAR(64)');
        $this->addSql('ALTER TABLE "user" ALTER password DROP NOT NULL');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP googleId');
        $this->addSql('ALTER TABLE "user" DROP hitobitoId');
        $this->addSql('ALTER TABLE "user" ALTER username TYPE VARCHAR(32)');
        $this->addSql('ALTER TABLE "user" ALTER password SET NOT NULL');
    }
}
