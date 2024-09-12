<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240912183023 extends AbstractMigration {
    public function getDescription(): string {
        return 'Checklist.IsPrototype';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checklist ADD isPrototype BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE checklist ALTER campid DROP NOT NULL');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE checklist DROP isPrototype');
        $this->addSql('ALTER TABLE checklist ALTER campId SET NOT NULL');
    }
}
