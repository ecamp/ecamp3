<?php

namespace eCamp\Core;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

class Module {
    public function getConfig() {
        return include __DIR__.'/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Application $app */
        $app = $e->getApplication();
        $events = $app->getEventManager();
        /** @var EntityManager $em */
        $em = $app->getServiceManager()->get('doctrine.entitymanager.orm_default');

        $events->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) use ($em) {
            $em->beginTransaction();
        });

        $events->attach(MvcEvent::EVENT_FINISH, function (MvcEvent $e) use ($em) {
            if ($e->getError()) {
                if ($em->getConnection()->isTransactionActive()) {
                    $em->rollback();
                }
            } else {
                $em->flush();
                if ($em->getConnection()->isTransactionActive()) {
                    $em->commit();
                }
            }
        });
    }
}
