<?php
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'factories' => [
        \EcampCore\DoctrineEventListener::class => InvokableFactory::class,
        \EcampCore\EventListener\CampEventListener::class => InvokableFactory::class,

        \EcampCore\Service\PeriodService::class => \EcampCore\Service\PeriodServiceFactory::class,
        \EcampCore\Service\DayService::class => \EcampCore\Service\DayServiceFactory::class,
    ],



];