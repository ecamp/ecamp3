<?php

$entityServicePath = __DIR__ . '/../src/EntityService';
$entityServiceAwarePath = __DIR__ . '/../src/EntityServiceAware';
$entityServiceTraitPath = __DIR__ . '/../src/EntityServiceTrait';

$services = glob($entityServicePath . '/*Service.php');

$invokeBody = '';

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


        $invokeBodyElement = file_get_contents(__DIR__ . '/EntityServiceInjectorBody.tpl');
        $invokeBodyElement = str_replace('[ServiceName]', $serviceName, $invokeBodyElement);
        $invokeBody .= $invokeBodyElement . PHP_EOL;
    }
}

$entityServiceInjector = file_get_contents(__DIR__ . '/EntityServiceInjector.tpl');
$entityServiceInjector = str_replace('[InvokeBody]', $invokeBody, $entityServiceInjector);

file_put_contents(__DIR__ . '/../src/ServiceManager/EntityServiceInjector.php', $entityServiceInjector);

