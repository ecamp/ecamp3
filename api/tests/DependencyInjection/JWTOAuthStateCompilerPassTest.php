<?php

namespace App\Tests\DependencyInjection;

use App\DependencyInjection\JWTOAuthStateCompilerPass;
use App\OAuth\JWTStateOAuth2Client;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
class JWTOAuthStateCompilerPassTest extends TestCase {
    public function testProcess() {
        // given
        $container = new ContainerBuilder();
        $container->prependExtensionConfig('knpu_oauth2_client', [
            'clients' => [
                'test-service' => [
                    'client_class' => JWTStateOAuth2Client::class,
                ],
                'test-service-2' => [
                    'client_class' => OAuth2Client::class,
                ],
            ],
        ]);
        $container->register('knpu.oauth2.client.test-service', JWTStateOAuth2Client::class);
        $container->getDefinition('knpu.oauth2.client.test-service')->addArgument('test argument');
        $container->register('knpu.oauth2.client.test-service-2', OAuth2Client::class);
        $container->getDefinition('knpu.oauth2.client.test-service-2')->addArgument('test argument');
        $container->register('knpu.oauth2.client.test-service-3', JWTStateOAuth2Client::class);
        $container->getDefinition('knpu.oauth2.client.test-service-3')->addArgument('test argument');
        $compilerPass = new JWTOAuthStateCompilerPass();

        // when
        $compilerPass->process($container);

        // then
        $this->assertEquals(6, count($container->getDefinition('knpu.oauth2.client.test-service')->getArguments()));
        $this->assertEquals(1, count($container->getDefinition('knpu.oauth2.client.test-service-2')->getArguments()));
        $this->assertEquals(1, count($container->getDefinition('knpu.oauth2.client.test-service-3')->getArguments()));
    }
}
