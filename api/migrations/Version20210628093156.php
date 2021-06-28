<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210628093156 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('TRUNCATE TABLE activity, camp, content_node, content_type, period, "user" CASCADE');
        $this->addSql('ALTER TABLE camp DROP CONSTRAINT FK_C1944230E05EFD25');
        $this->addSql('ALTER TABLE camp ADD CONSTRAINT FK_C1944230E05EFD25 FOREIGN KEY (ownerId) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE camp DROP CONSTRAINT fk_c1944230e05efd25');
        $this->addSql('ALTER TABLE camp ADD CONSTRAINT fk_c1944230e05efd25 FOREIGN KEY (ownerid) REFERENCES abstract_camp_owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
