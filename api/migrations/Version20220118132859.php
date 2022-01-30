<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220118132859 extends AbstractMigration {
    public function getDescription(): string {
        return 'Adds initial content types';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'44dcc7493c65\', \'SafetyConcept\', true, \'App\Entity\ContentNode\SingleText\', \'[]\', \'2022-01-18 09:12:56\', \'2022-01-18 09:12:56\');');
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'318e064ea0c9\', \'Storycontext\', true, \'App\Entity\ContentNode\SingleText\', \'[]\', \'2022-01-18 09:12:56\', \'2022-01-18 09:12:56\');');
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'4f0c657fecef\', \'Notes\', true, \'App\Entity\ContentNode\SingleText\', \'[]\', \'2022-01-18 09:12:56\', \'2022-01-18 09:12:56\');');
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'f17470519474\', \'ColumnLayout\', true, \'App\Entity\ContentNode\ColumnLayout\', \'[]\', \'2022-01-18 09:12:56\', \'2022-01-18 09:12:56\');');
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'cfccaecd4bad\', \'Storyboard\', true, \'App\Entity\ContentNode\Storyboard\', \'[]\', \'2022-01-18 09:12:56\', \'2022-01-18 09:12:56\');');
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'3ef17bd1df72\', \'Material\', true, \'App\Entity\ContentNode\MaterialNode\', \'[]\', \'2022-01-18 09:12:56\', \'2022-01-18 09:12:56\');');
        $this->addSql('INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime) VALUES (\'1a0f84e322c8\', \'LAThematicArea\', true, \'App\Entity\ContentNode\MultiSelect\', \'{"items":["outdoorTechnique","security","natureAndEnvironment","pioneeringTechnique","campsiteAndSurroundings","preventionAndIntegration"]}\', \'2022-01-18 09:12:56\', \'2022-01-18 09:12:56\');');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM public.content_type WHERE id IN (\'44dcc7493c65\', \'318e064ea0c9\', \'4f0c657fecef\', \'f17470519474\', \'cfccaecd4bad\', \'3ef17bd1df72\', \'1a0f84e322c8\')');
    }
}
