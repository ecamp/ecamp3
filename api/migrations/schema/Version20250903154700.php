<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250903154700 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add another view for filtering out person-related data in public camps';
    }

    public function up(Schema $schema): void {
        $this->addSql(
            <<<'EOF'
                    CREATE OR REPLACE VIEW public.view_user_camps
                    AS
                    select	cc.id::TEXT, cc.userid, cc.campid
                    from	camp_collaboration cc
                    where 	cc.status = 'established'
                EOF
        );
        $this->addSql(
            <<<'EOF'
                    CREATE OR REPLACE VIEW public.view_user_camps_with_public
                    AS
                    SELECT CONCAT(u.id, c.id) id, u.id userid, c.id campid
                    from camp c, "user" u
                    where c.isprototype = TRUE or c.isshared = TRUE
                    union all
                    select	cc.id, cc.userid, cc.campid
                    from	camp_collaboration cc
                    where 	cc.status = 'established'
                EOF
        );
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP VIEW IF EXISTS public.view_user_camps_with_public');
        $this->addSql(
            <<<'EOF'
                    CREATE OR REPLACE VIEW public.view_user_camps
                    AS
                    SELECT CONCAT(u.id, c.id) id, u.id userid,  c.id campid
                    from camp c, "user" u
                    where c.isprototype = TRUE
                    union all
                    select	cc.id, cc.userid, cc.campid
                    from	camp_collaboration cc
                    where 	cc.status = 'established'
                EOF
        );
    }
}
