<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261101182029 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add global image blobs and camp image associations';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE camp_image (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, unreferencedSince TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, campId VARCHAR(16) NOT NULL, imageBlobId VARCHAR(16) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_CB036EC39D468A55 ON camp_image (createTime)');
        $this->addSql('CREATE INDEX IDX_CB036EC355AA53E2 ON camp_image (updateTime)');
        $this->addSql('CREATE UNIQUE INDEX camp_image_camp_blob_unique ON camp_image (campId, imageBlobId)');
        $this->addSql('CREATE INDEX IDX_CB036EC36D299429 ON camp_image (campId)');
        $this->addSql('CREATE INDEX IDX_CB036EC3719F51AB ON camp_image (imageBlobId)');
        $this->addSql('CREATE TABLE image_blob (id VARCHAR(16) NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sha256 VARCHAR(64) NOT NULL, mediaType VARCHAR(255) NOT NULL, byteSize INT NOT NULL, width INT NOT NULL, height INT NOT NULL, unreferencedSince TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_42454F529D468A55 ON image_blob (createTime)');
        $this->addSql('CREATE INDEX IDX_42454F5255AA53E2 ON image_blob (updateTime)');
        $this->addSql('CREATE UNIQUE INDEX image_blob_sha256_unique ON image_blob (sha256)');
        $this->addSql('ALTER TABLE camp_image ADD CONSTRAINT FK_CB036EC36D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE camp_image ADD CONSTRAINT FK_CB036EC3719F51AB FOREIGN KEY (imageBlobId) REFERENCES image_blob (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE camp_image DROP CONSTRAINT FK_CB036EC36D299429');
        $this->addSql('ALTER TABLE camp_image DROP CONSTRAINT FK_CB036EC3719F51AB');
        $this->addSql('DROP TABLE camp_image');
        $this->addSql('DROP TABLE image_blob');
    }
}
