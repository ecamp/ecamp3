<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240413142253 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add View view_camp_root_content_nodes to fast resolve contentNode-Owner';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'EOF'
                    CREATE OR REPLACE VIEW public.view_camp_root_content_nodes
                    AS
                    SELECT a.campid, a.rootcontentnodeid
                    FROM activity a
                    UNION ALL
                    SELECT ca.campid, ca.rootcontentnodeid
                    FROM category ca
                EOF
        );
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP VIEW public.view_camp_root_content_nodes');
    }
}
