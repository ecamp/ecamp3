<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260620065038 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add form_test_datum table (shared singleton row for testing API form components)';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE form_test_datum (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text VARCHAR(255) DEFAULT NULL, multilineText TEXT DEFAULT NULL, html TEXT DEFAULT NULL, number INT DEFAULT NULL, flag BOOLEAN DEFAULT false NOT NULL, color VARCHAR(9) DEFAULT NULL, date VARCHAR(10) DEFAULT NULL, time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, language VARCHAR(16) DEFAULT NULL, languageMultiselect JSON NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_19A00CB39D468A55 ON form_test_datum (createTime)');
        $this->addSql('CREATE INDEX IDX_19A00CB355AA53E2 ON form_test_datum (updateTime)');

        // Seed the single shared row here (not via fixtures) so it exists in
        // every environment, including production. There is exactly one row and
        // all authenticated users read/write it.
        $this->addSql("INSERT INTO form_test_datum (id, createTime, updateTime, text, multilineText, html, number, flag, color, date, time, language, languageMultiselect) VALUES ('0123456789ab', '2024-01-01 00:00:00', '2024-01-01 00:00:00', 'Hello', 'Line one\nLine two', '<p>Rich <strong>text</strong></p>', 42, true, '#1976d2', '2024-01-15', '2024-01-15 09:30:00', 'en', '[\"en\", \"de\"]')");
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE form_test_datum');
    }
}
