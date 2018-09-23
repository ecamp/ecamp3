<?php

namespace eCamp\AoT;

use Psr\Container\ContainerInterface;
use Zend\Code\Scanner\DirectoryScanner;
use Zend\Di\CodeGenerator\InjectorGenerator;
use Zend\Di\Config;
use Zend\Di\ConfigInterface;
use Zend\Di\Definition\RuntimeDefinition;
use Zend\Di\Resolver\DependencyResolver;

require __DIR__ . '/../vendor/autoload.php';

function getClassNames(): iterable {
    // Define the source directories to scan for classes to generate
    // AoT factories for
    $directories = [
        __DIR__ . '/../module/eCampLib/src',
        __DIR__ . '/../module/eCampCore/src',
        __DIR__ . '/../module/eCampApi/src',
        __DIR__ . '/../module/eCampWeb/src',
    ];

    $scanner = new DirectoryScanner($directories);

    /** @var \Zend\Code\Scanner\ClassScanner $class */
    foreach ($scanner->getClasses() as $class) {
        yield $class->getName();
    }
}

// Generator dependencies. You might put this in a service factory
// in a real-life scenario.




/** @var ContainerInterface $container */
//$container = require __DIR__ . '/../config/modules.config.php';
//$config = $container->get(ConfigInterface::class);

$config = new Config();
$resolver = new DependencyResolver(new RuntimeDefinition(), $config);

// This is important; we want to use configured aliases of the service manager.
//$resolver->setContainer($container);

$generator = new InjectorGenerator($config, $resolver, __NAMESPACE__ . '\Generated');
$generator->setOutputDirectory(__DIR__ . '/../module/eCampAoT/gen');
$generator->generate(getClassNames());