<?php

$entityServicePath = __DIR__ . '/../src/EntityService';
$entityServiceAwarePath = __DIR__ . '/../src/EntityServiceAware';
$entityServiceTraitPath = __DIR__ . '/../src/EntityServiceTrait';
$entityServiceFactoryPath = __DIR__ . '/../src/EntityServiceFactory';

$services = glob($entityServicePath . '/*Service.php');

$injectBody = '';
$configBody = '';


foreach ($services as $service) {
    $serviceName = basename($service, '.php');

    if ($serviceName !== "AbstractEntityService") {
        $variableName = lcfirst($serviceName);

        $serviceAwareInterface = file_get_contents(__DIR__ . '/EntityServiceAware.tpl');
        $serviceAwareInterface = str_replace('[ServiceName]', $serviceName, $serviceAwareInterface);
        $serviceAwareInterface = str_replace('[ServiceVariable]', $variableName, $serviceAwareInterface);

        file_put_contents($entityServiceAwarePath . '/' . $serviceName . 'Aware.php', $serviceAwareInterface);


        $serviceTrait = file_get_contents(__DIR__ . '/EntityServiceTrait.tpl');
        $serviceTrait = str_replace('[ServiceName]', $serviceName, $serviceTrait);
        $serviceTrait = str_replace('[ServiceVariable]', $variableName, $serviceTrait);

        file_put_contents($entityServiceTraitPath . '/' . $serviceName . 'Trait.php', $serviceTrait);


        $serviceFactory = file_get_contents(__DIR__ . '/EntityServiceFactory.tpl');
        $serviceFactory = str_replace('[ServiceName]', $serviceName, $serviceFactory);

        file_put_contents($entityServiceFactoryPath . '/' . $serviceName . 'Factory.php', $serviceFactory);


        $injectBodyElement = file_get_contents(__DIR__ . '/EntityServiceInjectorBody.tpl');
        $injectBodyElement = str_replace('[ServiceName]', $serviceName, $injectBodyElement);
        $injectBody .= $injectBodyElement . PHP_EOL;


        $configBodyElement = file_get_contents(__DIR__ . '/EntityServiceConfigBody.tpl');
        $configBodyElement = str_replace('[ServiceName]', $serviceName, $configBodyElement);
        $configBody .= $configBodyElement . PHP_EOL;
    }
}

$entityServiceInjector = file_get_contents(__DIR__ . '/EntityServiceInjector.tpl');
$entityServiceInjector = str_replace('[InjectBody]', $injectBody, $entityServiceInjector);

$entityServiceConfig = file_get_contents(__DIR__ . '/EntityServiceConfig.tpl');
$entityServiceConfig = str_replace('[ServiceFactories]', $configBody, $entityServiceConfig);

file_put_contents(__DIR__ . '/../src/ServiceManager/EntityServiceInjector.php', $entityServiceInjector);
file_put_contents(__DIR__ . '/../config/generated/entityservices.config.php', $entityServiceConfig);

