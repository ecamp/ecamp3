<?php

$finder = PhpCsFixer\Finder::create()
    ->in('config')
    ->in('module')
    ->in('plugin')
    ->in('public')
;

return PhpCsFixer\Config::create()
    ->setFinder($finder)
;