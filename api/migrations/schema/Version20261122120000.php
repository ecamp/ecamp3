<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261122120000 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add camp images';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE camp_image (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, unreferencedSince TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, campId VARCHAR(16) NOT NULL, sha256 VARCHAR(64) NOT NULL, mediaType VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_CB036EC39D468A55 ON camp_image (createTime)');
        $this->addSql('CREATE INDEX IDX_CB036EC355AA53E2 ON camp_image (updateTime)');
        $this->addSql('CREATE UNIQUE INDEX camp_image_camp_sha256_unique ON camp_image (campId, sha256)');
        $this->addSql('CREATE INDEX IDX_CB036EC36D299429 ON camp_image (campId)');
        $this->addSql('CREATE INDEX camp_image_sha256_idx ON camp_image (sha256)');
        $this->addSql('ALTER TABLE camp_image ADD CONSTRAINT FK_CB036EC36D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE camp_image DROP CONSTRAINT FK_CB036EC36D299429');
        $this->addSql('DROP TABLE camp_image');
    }
}
