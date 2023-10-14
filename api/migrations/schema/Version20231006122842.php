<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231006122842 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add FlexLayout content type';
    }

    public function up(Schema $schema): void {
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'4211c1129939\', \'FlexLayout\', true, \'App\Entity\ContentNode\FlexLayout\', \'{"items":[{"slot":"1"},{"slot":"2"}],"variant":"main-aside"}\', \'2023-10-06 12:28:42\', \'2023-10-06 12:28:42\');');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM public.content_type WHERE id IN (\'4211c1129939\')');
    }
}
