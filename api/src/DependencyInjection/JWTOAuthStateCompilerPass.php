<?php

namespace App\DependencyInjection;

use App\OAuth\JWTStateOAuth2Client;
use App\Repository\OAuthStateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JWTOAuthStateCompilerPass implements CompilerPassInterface {
    /**
     * Read all configured OAuth services, and add more constructor arguments in order to support
     * our JWT-based session substitute.
     * See JWTStateOAuth2Client for more info.
     */
    public function process(ContainerBuilder $container) {
        foreach (array_keys($container->getExtensionConfig('knpu_oauth2_client')[0]['clients']) as $id) {
            $definition = $container->getDefinition('knpu.oauth2.client.'.$id);
            if (JWTStateOAuth2Client::class !== $definition->getClass()) {
                return;
            }

            $definition->addArgument('%env(COOKIE_PREFIX)%');
            $definition->addArgument('%kernel.environment%');
            $definition->addArgument(new Reference(JWTEncoderInterface::class));
            $definition->addArgument(new Reference(EntityManagerInterface::class));
            $definition->addArgument(new Reference(OAuthStateRepository::class));
        }
    }
}
