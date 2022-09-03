<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220830180349 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE o_auth_state (id VARCHAR(16) NOT NULL, state VARCHAR(255) NOT NULL, expireTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_466EF70C9D468A55 ON o_auth_state (createTime)');
        $this->addSql('CREATE INDEX IDX_466EF70C55AA53E2 ON o_auth_state (updateTime)');
        $this->addSql('CREATE INDEX IDX_466EF70CD49BE2B9A393D2FB ON o_auth_state (expireTime, state)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE o_auth_state');
    }
}
