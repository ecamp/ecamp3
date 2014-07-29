<?php

namespace EcampDB\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Zend\Mvc\Controller\AbstractActionController;

use Doctrine\Common\DataFixtures\Loader;

class FixturesController extends AbstractActionController
{

    private function loadFixtures($append = false)
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $loader = new Loader();

        $loader->loadFromDirectory( __DIR__ . '/../Fixtures' );
        $fixtures = $loader->getFixtures();

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);

        $executor->execute($fixtures, $append);
    }

    public function appendAction()
    {
        $this->loadFixtures(true);

        $this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
    }

    public function defaultAction()
    {
        $this->loadFixtures(false);

        $this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
    }

}
