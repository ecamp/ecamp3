<?php

namespace App\Symfony\DependencyInjection;

use App\Security\JWT\SplitCookieExtractor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Replaces the SplitCookieExtractor of the LexikJWTBundle with our improved version.
 * This will be obsoleted by https://github.com/lexik/LexikJWTAuthenticationBundle/pull/931.
 */
class SplitCookieCompilerPass implements CompilerPassInterface {
    public function process(ContainerBuilder $container) {
        $container
            ->getDefinition('lexik_jwt_authentication.extractor.split_cookie_extractor')
            ->setClass(SplitCookieExtractor::class)
        ;
    }
}
