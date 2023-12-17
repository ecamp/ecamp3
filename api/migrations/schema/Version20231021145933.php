<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231021145933 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove couponkey from camp';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX couponkey_unique');
        $this->addSql('ALTER TABLE camp DROP couponkey');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE camp ADD couponkey TEXT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX couponkey_unique ON camp (couponkey)');
    }
}
