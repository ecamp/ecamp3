<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125144627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_node_multiselect_option RENAME COLUMN pos TO position');
        $this->addSql('ALTER TABLE content_node_storyboard_section RENAME COLUMN pos TO position');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE content_node_multiselect_option RENAME COLUMN position TO pos');
        $this->addSql('ALTER TABLE content_node_storyboard_section RENAME COLUMN position TO pos');
    }
}
