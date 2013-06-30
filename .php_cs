<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('module')
    ->in('plugins')
    ->exclude('data')
;


return Symfony\CS\Config\Config::create()
	->fixers(array('-linefeed'))
    ->finder($finder)
;
