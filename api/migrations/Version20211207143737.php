<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207143737 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        $this->addSql('
                            CREATE TABLE "profile"
                            (
                                id         VARCHAR(16) NOT NULL,
                                email      VARCHAR(64) NOT NULL,
                                username   VARCHAR(32) NOT NULL,
                                firstname  TEXT        DEFAULT NULL,
                                surname    TEXT        DEFAULT NULL,
                                nickname   TEXT        DEFAULT NULL,
                                language   VARCHAR(20) DEFAULT NULL,
                                roles      JSON        NOT NULL,
                                createTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                                updateTime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                                PRIMARY KEY (id)
                            )
                            ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FE7927C74 ON "profile" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0FF85E0677 ON "profile" (username)');
        $this->addSql('CREATE INDEX IDX_8157AA0F9D468A55 ON "profile" (createTime)');
        $this->addSql('CREATE INDEX IDX_8157AA0F55AA53E2 ON "profile" (updateTime)');
        $this->addSql('
                            INSERT INTO "profile" (id, email, username, firstname, surname, nickname, language, roles, createTime, updateTime)
                            SELECT id, email, username, firstname, surname, nickname, language, roles, createTime, updateTime 
                            FROM "user" 
                            WHERE id NOT IN (SELECT id FROM "profile")
                            ');
        $this->addSql('DROP INDEX uniq_8d93d649f85e0677');
        $this->addSql('DROP INDEX uniq_8d93d649e7927c74');
        $this->addSql('ALTER TABLE "user" ADD profileId VARCHAR(16)');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6499B26949C FOREIGN KEY (profileId) REFERENCES "profile" (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6499B26949C ON "user" (profileId)');
        $this->addSql('UPDATE "user" u SET profileId = u.id');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN profileId SET NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP email');
        $this->addSql('ALTER TABLE "user" DROP username');
        $this->addSql('ALTER TABLE "user" DROP firstname');
        $this->addSql('ALTER TABLE "user" DROP surname');
        $this->addSql('ALTER TABLE "user" DROP nickname');
        $this->addSql('ALTER TABLE "user" DROP language');
        $this->addSql('ALTER TABLE "user" DROP roles');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE "user" ADD email VARCHAR(64)');
        $this->addSql('ALTER TABLE "user" ADD username VARCHAR(32)');
        $this->addSql('ALTER TABLE "user" ADD firstname TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD surname TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD nickname TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD language VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD roles JSON');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649f85e0677 ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');

        $this->addSql('
                            UPDATE "user" u
                            SET 
                                email=(SELECT email FROM "profile" where id = u.profileId),
                                username=(SELECT username FROM "profile" where id = u.profileId),
                                firstname=(SELECT firstname FROM "profile" where id = u.profileId),
                                surname=(SELECT surname FROM "profile" where id = u.profileId),
                                nickname=(SELECT nickname FROM "profile" where id = u.profileId),
                                language=(SELECT language FROM "profile" where id = u.profileId),
                                roles=(SELECT roles FROM "profile" where id = u.profileId)
        ');

        $this->addSql('ALTER TABLE "user" ALTER COLUMN email SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN username SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN roles SET NOT NULL');

        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6499B26949C');
        $this->addSql('DROP INDEX UNIQ_8D93D6499B26949C');
        $this->addSql('DROP TABLE "profile"');
        $this->addSql('ALTER TABLE "user" DROP profileId');
    }
}
