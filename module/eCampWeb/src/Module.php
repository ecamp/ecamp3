<?php

namespace eCamp\Web;

use eCamp\Core\Auth\AuthService;
use eCamp\Web\View\AuthUserInjector;
use eCamp\Web\View\TranslatorInjector;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param MvcEvent $e
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();
        $events = $app->getEventManager();
        $serviceLocator = $app->getServiceManager();


        /** @var AuthService $authService */
        $authService = $serviceLocator->get(AuthService::class);

        $authUserInjector = new AuthUserInjector($authService);
        $authUserInjector->attach($events);


        /** @var TranslatorInterface $translator */
        $translator = $serviceLocator->get(TranslatorInterface::class);

        $translatorInjector = new TranslatorInjector($translator);
        $translatorInjector->attach($events);
    }

}
