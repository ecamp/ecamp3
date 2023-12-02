<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231111000042 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add DefaultLayout content type';
    }

    public function up(Schema $schema): void {
        $this->addSql("
            INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime)
            VALUES (
                'a4211c112939',
                'DefaultLayout',
                true,
                'App\\Entity\\ContentNode\\DefaultLayout',
                '{ \"items\": [{ \"slot\": \"main\" },{ \"slot\": \"aside-top\" },{ \"slot\": \"aside-bottom\" }] }',
                '2023-11-11 00:00:42',
                '2023-11-11 00:00:42'
            );
        ");
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM public.content_type WHERE id IN (\'a4211c112939\')');
    }
}
