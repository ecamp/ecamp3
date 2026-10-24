<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260731145532 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add hitobitoProvider and hitobitoEventId to camp, hitobitoId to period';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE camp ADD hitobitoProvider VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE camp ADD hitobitoEventId VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX hitobitoprovider_hitobitoeventid_unique ON camp (hitobitoProvider, hitobitoEventId)');
        $this->addSql('ALTER TABLE period ADD hitobitoId VARCHAR(255) DEFAULT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX hitobitoprovider_hitobitoeventid_unique');
        $this->addSql('ALTER TABLE camp DROP hitobitoProvider');
        $this->addSql('ALTER TABLE camp DROP hitobitoEventId');
        $this->addSql('ALTER TABLE period DROP hitobitoId');
    }
}
