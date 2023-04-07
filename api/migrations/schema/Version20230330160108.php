<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330160108 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp ADD organizer TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE camp ADD kind TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE camp ADD coachName TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE camp ADD courseNumber TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE camp ADD courseKind TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE camp ADD trainingAdvisorName TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE camp ADD printYSLogoOnPicasso BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE camp DROP organizer');
        $this->addSql('ALTER TABLE camp DROP kind');
        $this->addSql('ALTER TABLE camp DROP coachName');
        $this->addSql('ALTER TABLE camp DROP courseNumber');
        $this->addSql('ALTER TABLE camp DROP courseKind');
        $this->addSql('ALTER TABLE camp DROP trainingAdvisorName');
        $this->addSql('ALTER TABLE camp DROP printYSLogoOnPicasso');
    }
}
