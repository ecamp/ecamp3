<?php

namespace eCamp\AoT;

use Psr\Container\ContainerInterface;
use Zend\Code\Scanner\DirectoryScanner;
use Zend\Di\CodeGenerator\InjectorGenerator;
use Zend\Di\Config;
use Zend\Di\ConfigInterface;
use Zend\Di\Definition\RuntimeDefinition;
use Zend\Di\Resolver\DependencyResolver;

//require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../autoload.php';

function getClassNames(ContainerInterface $container): iterable {
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

        /**
         * omit classes that 
         * - already have a factory (or abstract factory), e.g. discoverable by service manager without using Zend\DI
         * - are not instantiable (e.g. abstract classes)
         * - have no constructor
         * - have a constructor with 0 arguments
         */
        if(!$container->has($class->getName()) && $class->isInstantiable() && $class->getMethod('__construct') && $class->getMethod('__construct')->getNumberOfParameters()>0){
            printf("ADDING ".$class->getName()."\n");
            yield $class->getName();
        }
    }
}

/* we need the App without DI to check which factories are missing... */
$appWithoutDi = \eCampApp::CreateAppWithoutDi();

/* ... but for generating the code, we need the full App with DI */
$appWithDi = \eCampApp::CreateApp();

$container = $appWithDi->getServiceManager();
$config = $container->get('configuration');
$di_config = new Config( $config['dependencies']['auto'] );

$resolver = new DependencyResolver(new RuntimeDefinition(), $di_config);
$resolver->setContainer($container);

$generator = new InjectorGenerator($di_config, $resolver, __NAMESPACE__ . '\Generated');
$generator->setOutputDirectory(__DIR__ . '/../module/eCampAoT/gen');
$generator->generate(getClassNames($appWithoutDi->getServiceManager()));