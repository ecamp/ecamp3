<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260627120000 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add pg_trgm GIN indexes for case-insensitive partial profile search (firstname, surname, nickname, email)';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        $this->addSql('CREATE INDEX unmanaged_idx_profile_firstname_trgm ON profile USING gin (LOWER(firstname) gin_trgm_ops)');
        $this->addSql('CREATE INDEX unmanaged_idx_profile_surname_trgm ON profile USING gin (LOWER(surname) gin_trgm_ops)');
        $this->addSql('CREATE INDEX unmanaged_idx_profile_nickname_trgm ON profile USING gin (LOWER(nickname) gin_trgm_ops)');
        $this->addSql('CREATE INDEX unmanaged_idx_profile_email_trgm ON profile USING gin (LOWER(email) gin_trgm_ops)');
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql('DROP INDEX unmanaged_idx_profile_firstname_trgm');
        $this->addSql('DROP INDEX unmanaged_idx_profile_surname_trgm');
        $this->addSql('DROP INDEX unmanaged_idx_profile_nickname_trgm');
        $this->addSql('DROP INDEX unmanaged_idx_profile_email_trgm');
        $this->addSql('DROP EXTENSION IF EXISTS pg_trgm');
    }
}
