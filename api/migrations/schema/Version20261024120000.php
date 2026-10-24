<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261024120000 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add the J+S activity characteristics MultiSelect content type';
    }

    public function up(Schema $schema): void {
        $this->addSql(
            'INSERT INTO content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (?, ?, true, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
            ['c9a61096ef01', 'YSActivityCharacteristics', 'App\Entity\ContentNode\MultiSelect', '{"items":["outdoorTechnique","travellingSafety","gamesAndSports","gettingInvolved","community","natureAndEnvironment"]}']
        );
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql('DELETE FROM content_type WHERE id = ?', ['c9a61096ef01']);
    }
}
