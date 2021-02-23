<?php

namespace eCamp\Core;

use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\ORM\EntityManager;
use eCamp\Core\ContentType\ContentTypeStrategyProviderInjector;
use Laminas\ApiTools\ApiProblem\ApiProblemResponse;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;

class Module {
    public function getConfig() {
        return include __DIR__.'/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e): void {
        /** @var Application $app */
        $app = $e->getApplication();
        $events = $app->getEventManager();
        $sm = $app->getServiceManager();

        // Force loading the session config, in order to force using our custom config
        // See https://github.com/laminas/laminas-session/issues/15#issuecomment-569998935
        $sm->get(\Laminas\Session\ManagerInterface::class);

        /** @var EntityManager $em */
        $em = $sm->get('doctrine.entitymanager.orm_default');

        // Enable next line for Doctrine debug output
        // $em->getConfiguration()->setSQLLogger(new EchoSQLLogger());

        $events->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) use ($em): void {
            $em->beginTransaction();
        }, 10);

        $events->attach(MvcEvent::EVENT_FINISH, function (MvcEvent $e) use ($em): void {
            if ($e->getError() || $e->getResponse() instanceof ApiProblemResponse) {
                if ($em->getConnection()->isTransactionActive()) {
                    $em->getConnection()->rollback();
                }
            } else {
                $em->flush();
                if ($em->getConnection()->isTransactionActive()) {
                    $em->getConnection()->commit();
                }
            }
        }, 10);

        // inject ContentTypeStrategyProvider into Doctrine entities (mainly ActivityContent entity)
        $em->getEventManager()->addEventListener([\Doctrine\ORM\Events::postLoad], $sm->get(ContentTypeStrategyProviderInjector::class));
        $em->getEventManager()->addEventListener([\Doctrine\ORM\Events::prePersist], $sm->get(ContentTypeStrategyProviderInjector::class));
    }
}
