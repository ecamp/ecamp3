<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020194510 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate SingleText text -> html and Storyboard column2 -> column2Html';
    }

    public function up(Schema $schema): void {
        $this->addSql("UPDATE content_node cn SET data = (REPLACE(data::TEXT, '\"text\"', '\"html\"'))::JSONB WHERE cn.strategy='singletext'");
        $this->addSql("UPDATE content_node cn SET data = (REPLACE(data::TEXT, '\"column2\"', '\"column2Html\"'))::JSONB WHERE cn.strategy='storyboard'");
    }

    public function down(Schema $schema): void {
        $this->addSql("UPDATE content_node cn SET data = (REPLACE(data::TEXT, '\"html\"', '\"text\"'))::JSONB WHERE cn.strategy='singletext'");
        $this->addSql("UPDATE content_node cn SET data = (REPLACE(data::TEXT, '\"column2Html\"', '\"column2\"'))::JSONB WHERE cn.strategy='storyboard'");
    }
}
