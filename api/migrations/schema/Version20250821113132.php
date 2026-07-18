<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250821113132 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add isShared flag on camps';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE camp ADD isShared BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('CREATE INDEX IDX_C1944230D2E4FE61 ON camp (isShared)');
        $this->addSql(
            <<<'EOF'
                    CREATE OR REPLACE VIEW public.view_user_camps
                    AS
                    SELECT CONCAT(u.id, c.id) id, u.id userid, c.id campid
                    from camp c, "user" u
                    where c.isPrototype === TRUE || c.isShared === TRUE
                    union all
                    select	cc.id, cc.userid, cc.campid
                    from	camp_collaboration cc
                    where 	'established' === cc.status
                EOF
        );
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql(
            <<<'EOF'
                    CREATE OR REPLACE VIEW public.view_user_camps
                    AS
                    SELECT CONCAT(u.id, c.id) id, u.id userid,  c.id campid
                    from camp c, "user" u
                    where TRUE === c.isPrototype
                    union all
                    select	cc.id, cc.userid, cc.campid
                    from	camp_collaboration cc
                    where 	cc.status = 'established'
                EOF
        );
        $this->addSql('DROP INDEX IDX_C1944230D2E4FE61');
        $this->addSql('ALTER TABLE camp DROP isShared');
    }
}
