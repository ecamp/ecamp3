<?php

namespace App;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel implements CompilerPassInterface {
    use MicroKernelTrait;

    /**
     * In this method, we can add our own compiler pass code, i.e. customization to bundle service configs,
     * when this isn't possible via services.yaml.
     */
    public function process(ContainerBuilder $container) {
        // Add custom arguments to each of our OAuth clients, so they don't have to use native PHP sessions.
        // See JWTStateOAuth2Client for more info.
        foreach (array_keys($container->getExtensionConfig('knpu_oauth2_client')[0]['clients']) as $id) {
            $definition = $container->getDefinition('knpu.oauth2.client.'.$id);
            $definition->addArgument('%env(COOKIE_PREFIX)%');
            $definition->addArgument('%kernel.environment%');
            $definition->addArgument(new Reference(JWTEncoderInterface::class));
            $definition->addArgument(new Reference(EntityManagerInterface::class));
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_'.$this->environment.'.yaml');
        } else {
            $container->import('../config/{services}.php');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } else {
            $routes->import('../config/{routes}.php');
        }
    }

    protected function build(ContainerBuilder $container) {
        parent::build($container);
    }
}
