<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR2' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'curly_braces_position' => [
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line'
        ],
        'php_unit_test_class_requires_covers' => false,
        'no_superfluous_phpdoc_tags' => true,
    ])
    ->setFinder($finder)
    ;
