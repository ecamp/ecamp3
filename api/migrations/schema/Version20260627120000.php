<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds pg_trgm functional GIN indexes on the searchable profile columns so that the
 * case-insensitive, partial profile search (used by the collaborator invite autocomplete,
 * see App\Doctrine\Filter\ProfileSearchFilter) can be served by an index instead of a
 * sequential scan over all profiles.
 *
 * The indexes are created on LOWER(<column>) because the search uses LOWER(col) LIKE LOWER(:term)
 * (DQL has no ILIKE). They are prefixed with "unmanaged_" so App\Doctrine\DBAL\Schema\
 * CustomPostgreSQLSchemaManager hides them from Doctrine's schema diff and doctrine:schema:validate
 * stays green (functional/opclass indexes cannot be expressed in ORM mapping metadata).
 */
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
