<?php

namespace eCamp\AoT;

use Laminas\Code\Scanner\DirectoryScanner;
use Laminas\Di\CodeGenerator\InjectorGenerator;
use Psr\Container\ContainerInterface;

require __DIR__.'/../vendor/autoload.php';

function getClassNames(ContainerInterface $container): iterable {
    // Define the source directories to scan for classes to generate
    // AoT factories for
    $directories = [
        __DIR__.'/../module/eCampLib/src',
        __DIR__.'/../module/eCampCore/src',
        __DIR__.'/../module/eCampApi/src',
        __DIR__.'/../content-type/eCampStoryboard/src',
        __DIR__.'/../content-type/eCampSingleText/src',
    ];

    $scanner = new DirectoryScanner($directories);

    /** @var \Laminas\Code\Scanner\ClassScanner $class */
    foreach ($scanner->getClasses() as $class) {
        /*
         * omit classes that
         * - already have a factory (or abstract factory), e.g. discoverable by service manager without using Zend\DI
         * - are not instantiable (e.g. abstract classes)
         * - have no constructor
         * - have a constructor with 0 arguments
         */
        if (!$container->has($class->getName()) && $class->isInstantiable() && $class->getMethod('__construct') && $class->getMethod('__construct')->getNumberOfParameters() > 0) {
            printf('ADDING '.$class->getName()."\n");
            yield $class->getName();
        }
    }
}

// we need the App without DI to check which factories are missing...
$smWithoutDi = \eCampApp::CreateServiceManagerWithoutDi();

// ... but for generating the code, we need the full App with DI
$appWithDi = \eCampApp::CreateApp();

/** @var ContainerInterface $container */
$container = $appWithDi->getServiceManager();
$generator = $container->get(InjectorGenerator::class);
$generator->generate(getClassNames($smWithoutDi));
