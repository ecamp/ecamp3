<?php

namespace eCamp\Core;

use Doctrine\DBAL\Logging\EchoSQLLogger;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Doctrine\ORM\EntityManager;
use ZF\ApiProblem\ApiProblemResponse;
use eCamp\Core\Plugin\PluginStrategyProviderInjector;

class Module {
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Application $app */
        $app = $e->getApplication();
        $events = $app->getEventManager();
        $sm = $app->getServiceManager();
        
        /** @var EntityManager $em */
        $em = $sm->get('doctrine.entitymanager.orm_default');

        // Enable next line for Doctrine debug output
        // $em->getConfiguration()->setSQLLogger(new EchoSQLLogger());
        
        $em->beginTransaction();
       
        $events->attach(MvcEvent::EVENT_FINISH, function (MvcEvent $e) use ($em) {
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
        });

        // inject PluginStrategyProvider into Doctrine entities (mainly EventPlugin entity)
        $em->getEventManager()->addEventListener(array(\Doctrine\ORM\Events::postLoad), $sm->get(PluginStrategyProviderInjector::class));
        $em->getEventManager()->addEventListener(array(\Doctrine\ORM\Events::prePersist), $sm->get(PluginStrategyProviderInjector::class));
    }
}
