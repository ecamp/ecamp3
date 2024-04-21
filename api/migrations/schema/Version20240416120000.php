<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240416120000 extends AbstractMigration {
    public function getDescription(): string {
        return 'Adds new content types';
    }

    public function up(Schema $schema): void {
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'c462edd869f3\', \'LearningObjectives\', true, \'App\Entity\ContentNode\SingleText\', \'[]\', \'2024-04-16 12:00:00\', \'2024-04-16 12:00:00\');');
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'5e2028c55ee4\', \'LearningTopics\', true, \'App\Entity\ContentNode\SingleText\', \'[]\', \'2024-04-16 12:00:00\', \'2024-04-16 12:00:00\');');
    }

    public function down(Schema $schema): void {
        $this->addSql('DELETE FROM public.content_type WHERE id IN (\'c462edd869f3\', \'5e2028c55ee4\')');
    }
}
