<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240414092957 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add View view_user_camps to fast resolve visible camps';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
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

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP VIEW public.view_user_camps');
    }
}
