<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240620143000 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add ChecklistNode content type';
    }

    public function up(Schema $schema): void {
        $this->addSql("
            INSERT INTO public.content_type (id, name, active, entityclass, jsonconfig, createtime, updatetime)
            VALUES (
                'a4211c11211c',
                'Checklist',
                true,
                'App\\Entity\\ContentNode\\ChecklistNode',
                null,
                '2024-06-16 14:30:00',
                '2024-06-16 14:30:00'
            );
        ");
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM public.content_type WHERE id IN (\'a4211c11211c\')');
    }
}
