<?php

namespace DataMigrations;

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;

function createTruncateDatabaseCommand(): string {
    return "
            DO $$ DECLARE
                r RECORD;
            BEGIN
                FOR r IN (
                    SELECT tablename 
                    FROM pg_tables 
                    WHERE 
                        schemaname = current_schema() 
                        AND tablename != 'doctrine_migration_versions'
                        AND tablename != 'content_type') 
                LOOP
                    EXECUTE 'TRUNCATE TABLE ' || quote_ident(r.tablename) || ' CASCADE';
                END LOOP;
            END $$;
        ";
}

function getStatementsForMigrationFile(string $migrationFile): array {
    $dump_file = str_replace('.php', '_data.sql', $migrationFile);
    $sql = file_get_contents($dump_file);

    $parser = new Parser($sql);
    $parsed_statements = array_map(fn (InsertStatement $statement) => $statement->build(), $parser->statements);

    return str_replace('`', '"', $parsed_statements);
}
