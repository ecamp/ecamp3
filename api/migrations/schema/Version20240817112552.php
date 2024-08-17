<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240817112552 extends AbstractMigration {
    public function getDescription(): string {
        return 'Rename camp.name to shortTitle and switch shortTitle with title if shortTitle is longer than 16 characters and title is shorter than shortTitle';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE camp RENAME COLUMN name TO shortTitle');
        $this->addSql('ALTER TABLE camp ALTER shortTitle DROP NOT NULL, ALTER shortTitle TYPE TEXT');
        $this->addSql('UPDATE camp SET shortTitle = null WHERE shortTitle = title');
        $this->addSql('UPDATE camp SET shortTitle = title, title = shortTitle WHERE char_length(shortTitle) > 16 AND char_length(title) < char_length(shortTitle);');
        // $this->addSql('UPDATE camp SET shortTitle = left(shortTitle, 16) WHERE char_length(shortTitle) > 16;');
    }

    public function down(Schema $schema): void {
        $this->addSql("UPDATE camp SET shortTitle = '' WHERE shortTitle IS NULL");
        $this->addSql('ALTER TABLE camp ALTER shortTitle SET NOT NULL, ALTER shortTitle TYPE VARCHAR(32)');
        $this->addSql('ALTER TABLE camp RENAME COLUMN shortTitle TO name');
    }
}
