<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('gen')
    ->in('config')
    ->in('module')
    ->in('plugin')
    ->in('public')
;

return PhpCsFixer\Config::create()
    ->setRules([
        'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
    ])
    ->setFinder($finder)
;