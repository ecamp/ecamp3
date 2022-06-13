<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210070753 extends AbstractMigration {
    public function getDescription(): string {
        return 'Adds OAuth provider id properties';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profile ADD googleId VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD pbsmidataId VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD cevidbId VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ALTER username TYPE VARCHAR(64)');
        $this->addSql('ALTER TABLE "user" ALTER password DROP NOT NULL');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ALTER password SET NOT NULL');
        $this->addSql('ALTER TABLE "profile" DROP googleId');
        $this->addSql('ALTER TABLE "profile" DROP pbsmidataId');
        $this->addSql('ALTER TABLE "profile" DROP cevidbId');
        $this->addSql('ALTER TABLE "profile" ALTER username TYPE VARCHAR(32)');
    }
}
