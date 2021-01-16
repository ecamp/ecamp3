<?php

namespace eCamp\AoT;

use Exception;
use Laminas\Di\CodeGenerator\InjectorGenerator;
use OutOfBoundsException;
use Psr\Container\ContainerInterface;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

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

    $astLocator = (new BetterReflection())->astLocator();

    /** @var \Composer\Autoload\ClassLoader $classLoader */
    $classLoader = require __DIR__.'/../vendor/autoload.php';
    $composerClassReflector = new ClassReflector(new ComposerSourceLocator($classLoader, $astLocator));

    $directoriesSourceLocator = new DirectoriesSourceLocator($directories, $astLocator);
    $directoriesReflector = new ClassReflector($directoriesSourceLocator);
    $classes = $directoriesReflector->getAllClasses();

    /** @var ReflectionClass $class */
    foreach ($classes as $class) {
        /*
        * omit classes that
        * - already have a factory (or abstract factory), e.g. discoverable by service manager without using Zend\DI
        * - are not instantiable (e.g. abstract classes)
        * - have no constructor
        * - have a constructor with 0 arguments
        */
        $reflectionClass = $composerClassReflector->reflect($class->getName());

        if (!$container->has($reflectionClass->getName())) {
            try {
                if ($reflectionClass->isInstantiable()) {
                    $constructor = $reflectionClass->getConstructor();
                    if ($constructor->getNumberOfParameters() > 0) {
                        printf('ADDING '.$class->getName()."\n");
                        yield $reflectionClass->getName();
                    }
                }
            } catch (OutOfBoundsException $x) {
                // no constructor found
            } catch (Exception $ex) {
                // some other exception
                printf('SKIP   '.$class->getName()."\n");
            }
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
