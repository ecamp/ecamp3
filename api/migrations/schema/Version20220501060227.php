<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220501060227 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abstract_content_node_owner DROP CONSTRAINT FK_8E710AB4F886581C');
        $this->addSql('ALTER TABLE abstract_content_node_owner ADD CONSTRAINT FK_8E710AB4F886581C FOREIGN KEY (rootContentNodeId) REFERENCES content_node_columnlayout (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX idx_c19442309d468a55');
        $this->addSql('DROP INDEX idx_c93898a9d468a55');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT FK_481D0580B7939B21');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT FK_481D0580B7939B21 FOREIGN KEY (rootId) REFERENCES content_node_columnlayout (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX idx_10a0952d56778c5c');
        $this->addSql('DROP INDEX idx_d7785d2c55aa53e2');
        $this->addSql('DROP INDEX idx_d7785d2c9d468a55');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_10A0952D56778C5C');
        $this->addSql('CREATE INDEX idx_c93898a9d468a55 ON camp_collaboration (createtime)');
        $this->addSql('ALTER TABLE content_node DROP CONSTRAINT fk_481d0580b7939b21');
        $this->addSql('ALTER TABLE content_node ADD CONSTRAINT fk_481d0580b7939b21 FOREIGN KEY (rootid) REFERENCES content_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE abstract_content_node_owner DROP CONSTRAINT fk_8e710ab4f886581c');
        $this->addSql('ALTER TABLE abstract_content_node_owner ADD CONSTRAINT fk_8e710ab4f886581c FOREIGN KEY (rootcontentnodeid) REFERENCES content_node (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_c19442309d468a55 ON camp (createtime)');
        $this->addSql('CREATE INDEX idx_d7785d2c55aa53e2 ON schedule_entry (updatetime)');
        $this->addSql('CREATE INDEX idx_d7785d2c9d468a55 ON schedule_entry (createtime)');
    }
}
