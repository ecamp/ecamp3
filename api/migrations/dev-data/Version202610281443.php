<?php

declare(strict_types=1);

namespace DataMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

require_once __DIR__.'/helpers.php';

final class Version202610281443 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add comments to the GRGR 2027 camp in the dev data';
    }

    public function up(Schema $schema): void {
        // START PHP CODE
        $this->addSql(createTruncateDatabaseCommand());

        $statements = getStatementsForMigrationFile();
        foreach ($statements as $statement) {
            if (trim($statement)) {
                $this->addSql($statement);
            }
        }
        // END PHP CODE
    }

    #[\Override]
    public function down(Schema $schema): void {}
}
