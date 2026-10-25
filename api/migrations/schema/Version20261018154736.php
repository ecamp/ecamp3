<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261018154736 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add comment threads and resolving, link comment authors to camp collaborations instead of users';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526ca196f9fd');
        $this->addSql('ALTER TABLE comment ADD parentId VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD resolvedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD resolvedById VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD authorDisplayName TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ALTER authorId DROP NOT NULL');

        $this->addSql(<<<'SQL'
            UPDATE comment c
            SET authorId = cc.id
            FROM camp_collaboration cc
            WHERE cc.campId = c.campId AND cc.userId = c.authorId
            SQL);

        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C10EE4CEE FOREIGN KEY (parentId) REFERENCES comment (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C31AD1E75 FOREIGN KEY (resolvedById) REFERENCES camp_collaboration (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA196F9FD FOREIGN KEY (authorId) REFERENCES camp_collaboration (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_9474526C10EE4CEE ON comment (parentId)');
        $this->addSql('CREATE INDEX IDX_9474526C31AD1E75 ON comment (resolvedById)');
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CA196F9FD');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C31AD1E75');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C10EE4CEE');
        $this->addSql('DROP INDEX IDX_9474526C10EE4CEE');
        $this->addSql('DROP INDEX IDX_9474526C31AD1E75');
        $this->addSql(<<<'SQL'
            UPDATE comment c
            SET authorId = cc.userId
            FROM camp_collaboration cc
            WHERE cc.id = c.authorId
            SQL);
        $this->addSql('ALTER TABLE comment DROP parentId');
        $this->addSql('ALTER TABLE comment DROP resolvedAt');
        $this->addSql('ALTER TABLE comment DROP resolvedById');
        $this->addSql('ALTER TABLE comment DROP authorDisplayName');
        $this->addSql('ALTER TABLE comment ALTER authorid SET NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526ca196f9fd FOREIGN KEY (authorid) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
