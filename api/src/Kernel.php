<?php

namespace App;

use App\DependencyInjection\JWTOAuthStateCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel {
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container) {
        parent::build($container);

        $container->addCompilerPass(new JWTOAuthStateCompilerPass());
    }
}
