<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211221131558 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX user_camp_unique ON camp_collaboration (userId, campId)');
        $this->addSql('CREATE UNIQUE INDEX inviteEmail_camp_unique ON camp_collaboration (inviteEmail, campId)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX user_camp_unique');
        $this->addSql('DROP INDEX inviteEmail_camp_unique');
    }
}
