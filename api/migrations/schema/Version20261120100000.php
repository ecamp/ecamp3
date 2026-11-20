<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261120100000 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add MultiSelect content types for Swiss scout relationships and methods';
    }

    public function up(Schema $schema): void {
        $sql = 'INSERT INTO content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (?, ?, true, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';

        $this->addSql($sql, [
            '3656a8e17c4c',
            'PBSRelations',
            'App\Entity\ContentNode\MultiSelect',
            '{"items":["personality","body","otherPeople","spirituality","environment"]}',
        ]);
        $this->addSql($sql, [
            'bd916d78ceea',
            'PBSMethods',
            'App\Entity\ContentNode\MultiSelect',
            '{"items":["personalDevelopment","lawAndPromise","lifeInTheLittleGroup","ritualsAndSymbolism","learningByDoing","outdoorLife","gamesAndMovement","serviceToTheCommunity"]}',
        ]);
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql('DELETE FROM content_type WHERE id IN (?, ?)', ['3656a8e17c4c', 'bd916d78ceea']);
    }
}
