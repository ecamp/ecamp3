<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211030082112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D058010EE4CEE');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D058010EE4CEE FOREIGN KEY (parentId) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT fk_481d058010ee4cee');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT fk_481d058010ee4cee FOREIGN KEY (parentid) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
