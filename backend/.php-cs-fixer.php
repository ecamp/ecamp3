<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('gen')
    ->in(__DIR__.'/config')
    ->in(__DIR__.'/module')
    ->in(__DIR__.'/content-type')
    ->in(__DIR__.'/public')
;
$config = new \PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR2' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
        'php_unit_test_class_requires_covers' => false,
        'no_superfluous_phpdoc_tags' => true,
    ])
    ->setFinder($finder)
;