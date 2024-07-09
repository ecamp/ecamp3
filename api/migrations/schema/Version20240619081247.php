<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619081247 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove unused column collaborationacceptedby from camp_collaboration';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE camp_collaboration DROP collaborationacceptedby');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE camp_collaboration ADD collaborationacceptedby TEXT DEFAULT NULL');
    }
}
