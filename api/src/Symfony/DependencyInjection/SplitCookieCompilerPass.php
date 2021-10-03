<?php

namespace App\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SplitCookieCompilerPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container) {
        $container
            ->getDefinition('lexik_jwt_authentication.extractor.split_cookie_extractor')
            ->setClass(\App\Security\JWT\SplitCookieExtractor::class)
        ;
    }
}
