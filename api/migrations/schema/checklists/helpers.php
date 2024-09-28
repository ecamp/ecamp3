<?php

namespace DoctrineMigrations;

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statement;

function getStatementsForMigrationFile(string $sqlFile): array {
    $sql = file_get_contents($sqlFile);

    $parser = new Parser($sql);
    $parsed_statements = array_map(fn (Statement $statement) => $statement->build(), $parser->statements);

    return str_replace('`', '"', $parsed_statements);
}
