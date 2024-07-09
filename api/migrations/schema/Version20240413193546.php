<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240413193546 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add View view_period_material_items to fast resolve materialItem-Owner';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'EOF'
                    CREATE OR REPLACE VIEW public.view_period_material_items
                    AS
                    (
                        select distinct CONCAT(s.periodid, m.id) id, s.periodid, m.id materialitemid
                        from material_item m
                        join content_node n on n.id = m.materialnodeid
                        join activity a on a.rootcontentnodeid = n.rootid
                        join schedule_entry s on s.activityid = a.id
                    )
                    union all
                    (
                        select CONCAT(m.periodid, m.id) id, m.periodid, m.id materialitemid
                        from material_item m
                        where m.periodid is not null
                    )
                EOF
        );
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP VIEW public.view_period_material_items');
    }
}
