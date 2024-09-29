<?php

declare(strict_types=1);

namespace DataMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

require_once __DIR__.'/helpers.php';

final class Version202409281917 extends AbstractMigration {
    public function getDescription(): string {
        return 'Set color & abbreviation to some profiles';
    }

    public function up(Schema $schema): void {
        // START PHP CODE
        // END PHP CODE
    }

    public function down(Schema $schema): void {}
}
