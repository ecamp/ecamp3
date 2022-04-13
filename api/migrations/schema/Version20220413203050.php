<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413203050 extends AbstractMigration {
    public function getDescription(): string {
        return 'MaterialList (name not nullable; index for campCollaboration) // follow-up for PR 2452';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_10a0952d56778c5c');
        $this->addSql('ALTER TABLE material_list ALTER name SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_10A0952D56778C5C ON material_list (campCollaborationId)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_10A0952D56778C5C');
        $this->addSql('ALTER TABLE material_list ALTER name DROP NOT NULL');
        $this->addSql('CREATE INDEX idx_10a0952d56778c5c ON material_list (campcollaborationid)');
    }
}
