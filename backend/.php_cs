<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('gen')
    ->in(__DIR__.'/config')
    ->in(__DIR__.'/module')
    ->in(__DIR__.'/plugin')
    ->in(__DIR__.'/public')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
    ])
    ->setFinder($finder)
;