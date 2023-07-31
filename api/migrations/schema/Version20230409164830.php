<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230409164830 extends AbstractMigration {
    public function getDescription(): string {
        return 'Create ActivityProgressLabels for camps from their prototype';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE EXTENSION iF NOT EXISTS pgcrypto');

        // Test:
        // J+S
        $this->addSql('INSERT INTO activity_progress_label (id, campId, position, title, createTime, updateTime) 
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'e5027d852487\', 1, \'In Planung\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'e5027d852487\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'e5027d852487\', 2, \'Geplant\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'e5027d852487\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'e5027d852487\', 3, \'Lagerleiter:in OK\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'e5027d852487\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'e5027d852487\', 4, \'Coach OK\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'e5027d852487\'
        ');

        // Prod:
        // J+S Lager (Deutsch)
        $this->addSql('INSERT INTO activity_progress_label (id, campId, position, title, createTime, updateTime) 
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'75b3572a338e\', 1, \'In Planung\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'75b3572a338e\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'75b3572a338e\', 2, \'Geplant\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'75b3572a338e\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'75b3572a338e\', 3, \'Lagerleiter:in OK\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'75b3572a338e\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'75b3572a338e\', 4, \'Coach OK\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'75b3572a338e\'
        ');

        // Camp J+S (français)
        $this->addSql('INSERT INTO activity_progress_label (id, campId, position, title, createTime, updateTime) 
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'497f974e7d5d\', 1, \'En cours de planification\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'497f974e7d5d\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'497f974e7d5d\', 2, \'Planifié\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'497f974e7d5d\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'497f974e7d5d\', 3, \'Chef de camp OK\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'497f974e7d5d\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'497f974e7d5d\', 4, \'Coach OK\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'497f974e7d5d\'
        ');

        // Basic
        $this->addSql('INSERT INTO activity_progress_label (id, campId, position, title, createTime, updateTime) 
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'f92fe1cd1ae9\', 1, \'In Planung\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'f92fe1cd1ae9\'
            UNION
            SELECT  encode(gen_random_bytes(6), \'hex\'), \'f92fe1cd1ae9\', 2, \'Planung abgeschlossen\', 
                    date_trunc(\'second\', now()::timestamp), date_trunc(\'second\', now()::timestamp)
            FROM    camp c
            WHERE   c.id = \'f92fe1cd1ae9\'
        ');

        // All ohter camps
        $this->addSql('INSERT INTO activity_progress_label (id, campId, position, title, createTime, updateTime) 
            SELECT  encode(gen_random_bytes(6), \'hex\')
            ,       c.id
            ,       lp.position
            ,       lp.title
            ,       date_trunc(\'second\', now()::timestamp)
            ,       date_trunc(\'second\', now()::timestamp)
            FROM camp c
            JOIN camp cp on cp.id = c.campprototypeid
            JOIN activity_progress_label lp on lp.campid = cp.id
            WHERE not exists (select 1 from activity_progress_label l where l.campid = c.id)
        ');
    }

    public function down(Schema $schema): void {
    }
}
