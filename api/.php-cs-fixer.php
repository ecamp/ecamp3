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
        'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
        'php_unit_test_class_requires_covers' => false,
        'no_superfluous_phpdoc_tags' => true,
    ])
    ->setFinder($finder)
    ;
