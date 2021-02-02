<?php

namespace eCamp\AoT;

use Exception;
use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Mvc\Application;
use Laminas\ServiceManager\ServiceManager;
use OutOfBoundsException;
use Psr\Container\ContainerInterface;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;

class FactoryBuilder {
    private Application $app;
    private ServiceManager $services;
    private bool $verbose = false;

    public function __construct() {
        $this->app = \eCampApp::CreateApp();
        $this->services = \eCampApp::CreateServiceManagerWithoutDi();
    }

    public function setVerbose(bool $verbose = true) {
        $this->verbose = $verbose;
    }

    public function useTempFolder() {
        $cnf = $this->app->getConfig();
        $cnf['dependencies']['auto']['aot']['directory'] .= '_tmp';

        $allowOverride = $this->app->getServiceManager()->getAllowOverride();
        $this->app->getServiceManager()->setAllowOverride(true);
        $this->app->getServiceManager()->setService('config', $cnf);
        $this->app->getServiceManager()->setAllowOverride($allowOverride);
    }

    public function build() {
        /** @var ContainerInterface $container */
        $container = $this->app->getServiceManager();
        $generator = $container->get(InjectorGenerator::class);
        $generator->generate($this->getClassNames());
    }

    private function getClassNames(): iterable {
        // Define the source directories to scan for classes to generate
        // AoT factories for
        $directories = [
            __DIR__.'/../../eCampLib/src',
            __DIR__.'/../../eCampCore/src',
            __DIR__.'/../../eCampApi/src',
            __DIR__.'/../../../content-type/eCampStoryboard/src',
            __DIR__.'/../../../content-type/eCampSingleText/src',
        ];

        $astLocator = (new BetterReflection())->astLocator();

        /** @var \Composer\Autoload\ClassLoader $classLoader */
        $classLoader = require __DIR__.'/../../../vendor/autoload.php';
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

            if (!$this->services->has($reflectionClass->getName())) {
                try {
                    if ($reflectionClass->isInstantiable()) {
                        $constructor = $reflectionClass->getConstructor();
                        if ($constructor->getNumberOfParameters() > 0) {
                            $this->verbose && printf('ADDING '.$class->getName()."\n");
                            yield $reflectionClass->getName();
                        }
                    }
                } catch (OutOfBoundsException $x) {
                    // no constructor found
                } catch (Exception $ex) {
                    // some other exception
                    $this->verbose && printf('SKIP   '.$class->getName()."\n");
                }
            }
        }
    }
}
