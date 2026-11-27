<?php

namespace DataMigrations;

function getStatementsForMigrationFile(): array {
    $dump_file = __DIR__.'/data.sql';
    $file = fopen($dump_file, 'rb');
    if (false === $file) {
        throw new \RuntimeException('Unable to open dev data dump');
    }

    try {
        return getInsertStatementsFromStream($file);
    } finally {
        fclose($file);
    }
}

function getInsertStatementsFromStream($file): array {
    $statements = [];
    foreach (getSqlStatements($file) as $sql) {
        if (!preg_match('/\A(?:\s|--[^\r\n]*(?:\r\n|\r|\n)|#[^\r\n]*(?:\r\n|\r|\n)|\/\*.*?\*\/)*INSERT\b/is', $sql)) {
            continue;
        }

        $sql = preg_replace('/\A(?:\s|--[^\r\n]*(?:\r\n|\r|\n)|#[^\r\n]*(?:\r\n|\r|\n)|\/\*.*?\*\/)*/is', '', $sql);
        // @noinspection PhpPipeOperatorCanBeUsedInspection
        $statements[] = rtrim(convertBacktickIdentifiers(trim($sql)), "; \t\n\r\0\x0B");
    }

    return $statements;
}

function convertBacktickIdentifiers(string $sql): string {
    $quote = null;
    $escaped = false;
    $converted = '';

    for ($index = 0, $length = strlen($sql); $index < $length; ++$index) {
        $character = $sql[$index];
        if (null !== $quote) {
            if ($escaped) {
                $escaped = false;
            } elseif ('\\' === $character) {
                $escaped = true;
            } elseif ($character === $quote) {
                $converted .= '`' === $quote ? '"' : $character;
                $quote = null;

                continue;
            }
            $converted .= $character;
        } elseif ('`' === $character) {
            $quote = '`';
            $converted .= '"';
        } else {
            if ("'" === $character || '"' === $character) {
                $quote = $character;
            }
            $converted .= $character;
        }
    }

    return $converted;
}

function getSqlStatements($file): \Generator {
    $statement = '';
    $quote = null;
    $escaped = false;
    $line_comment = false;
    $block_comment = false;
    $previous = '';
    $next_character = null;

    while (true) {
        if (null !== $next_character) {
            $character = $next_character;
            $next_character = null;
        } else {
            $character = fgetc($file);
        }
        if (false === $character) {
            break;
        }

        $statement .= $character;

        if ($line_comment) {
            if ("\n" === $character) {
                $line_comment = false;
            }
        } elseif ($block_comment) {
            if ('*' === $previous && '/' === $character) {
                $block_comment = false;
            }
        } elseif (null !== $quote) {
            if ($escaped) {
                $escaped = false;
            } elseif ('\\' === $character) {
                $escaped = true;
            } elseif ($character === $quote) {
                $next_character = fgetc($file);
                if ($character === $next_character) {
                    $statement .= $next_character;
                    $next_character = null;
                } else {
                    $quote = null;
                }
            }
        } elseif ("'" === $character || '"' === $character || '`' === $character) {
            $quote = $character;
        } elseif ('/' === $previous && '*' === $character) {
            $block_comment = true;
        } elseif ('-' === $previous && '-' === $character) {
            $next_character = fgetc($file);
            $line_comment = false === $next_character || ord($next_character) <= 32;
        } elseif ('#' === $character) {
            $line_comment = true;
        } elseif (';' === $character) {
            yield $statement;
            $statement = '';
        }

        $previous = $character;
    }

    if ('' !== trim($statement)) {
        yield $statement;
    }
}
