<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261024121150 extends AbstractMigration {
    #[\Override]
    public function getDescription(): string {
        return 'Add YSPhysicalActivityDuration content type';
    }

    public function up(Schema $schema): void {
        $this->addSql("
            INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime)
            VALUES (
                'e87751a6291b',
                'YSPhysicalActivityDuration',
                true,
                'App\\Entity\\ContentNode\\NoData',
                null,
                '2026-10-24 12:00:00',
                '2026-10-24 12:00:00'
            );
        ");
    }

    #[\Override]
    public function down(Schema $schema): void {
        $this->addSql("DELETE FROM public.category_contenttype WHERE contenttype_id IN ('e87751a6291b')");
        $this->addSql("DELETE FROM public.content_node WHERE contenttypeid IN ('e87751a6291b')");
        $this->addSql("DELETE FROM public.content_type WHERE id IN ('e87751a6291b')");
    }
}
