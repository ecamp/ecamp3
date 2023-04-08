<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230408180319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_progress_label (id VARCHAR(16) NOT NULL, position INT NOT NULL, title TEXT NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, campId VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_701777B36D299429 ON activity_progress_label (campId)');
        $this->addSql('CREATE INDEX IDX_701777B39D468A55 ON activity_progress_label (createTime)');
        $this->addSql('CREATE INDEX IDX_701777B355AA53E2 ON activity_progress_label (updateTime)');
        $this->addSql('ALTER TABLE activity_progress_label ADD CONSTRAINT FK_701777B36D299429 FOREIGN KEY (campId) REFERENCES camp (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE activity ADD progressLabelId VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A5016EA8D FOREIGN KEY (progressLabelId) REFERENCES activity_progress_label (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AC74095A5016EA8D ON activity (progressLabelId)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095A5016EA8D');
        $this->addSql('ALTER TABLE activity_progress_label DROP CONSTRAINT FK_701777B36D299429');
        $this->addSql('DROP TABLE activity_progress_label');
        $this->addSql('DROP INDEX IDX_AC74095A5016EA8D');
        $this->addSql('ALTER TABLE activity DROP progressLabelId');
    }
}
