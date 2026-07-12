<?php

declare(strict_types=1);

namespace DataMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

require_once __DIR__.'/helpers.php';

final class Version202607130046 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Move dev data camps ten years forward';
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
