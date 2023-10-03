<?php

namespace DataMigrations;

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;

function getStatementsForMigrationFile(): array {
    $dump_file = __DIR__.'/data.sql';
    $sql = file_get_contents($dump_file);

    $parser = new Parser($sql);
    $parsed_statements = array_map(fn (InsertStatement $statement) => $statement->build(), $parser->statements);

    return str_replace('`', '"', $parsed_statements);
}
