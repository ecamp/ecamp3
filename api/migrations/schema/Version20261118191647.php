<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261118191647 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Doctrine ORM 3.7.6 fixed indexes from BaseEntity';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE INDEX IDX_C19442309D468A55 ON camp (createTime)');
        $this->addSql('CREATE INDEX IDX_C93898A9D468A55 ON camp_collaboration (createTime)');
        $this->addSql('CREATE INDEX IDX_D7785D2C9D468A55 ON schedule_entry (createTime)');
        $this->addSql('CREATE INDEX IDX_D7785D2C55AA53E2 ON schedule_entry (updateTime)');
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql('DROP INDEX IDX_C19442309D468A55');
        $this->addSql('DROP INDEX IDX_C93898A9D468A55');
        $this->addSql('DROP INDEX IDX_D7785D2C9D468A55');
        $this->addSql('DROP INDEX IDX_D7785D2C55AA53E2');
    }
}
