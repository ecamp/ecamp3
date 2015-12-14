<?php

namespace EcampDB\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Zend\Mvc\Controller\AbstractActionController;

use Doctrine\Common\DataFixtures\Loader;

class FixturesController extends AbstractActionController
{

    private function loadFixtures($directory, $append = false)
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $loader = new Loader();

        $loader->loadFromDirectory($directory);
        $fixtures = $loader->getFixtures();

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);

        $executor->execute($fixtures, $append);
    }

    public function appendProdAction()
    {
        $this->loadFixtures(__DIR__ . '/../Fixtures/Prod', true);
        $this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
    }

    public function replaceProdAction()
    {
        $this->loadFixtures(__DIR__ . '/../Fixtures/Prod', false);
        $this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
    }

    public function appendTestAction()
    {
        $this->loadFixtures(__DIR__ . '/../Fixtures/Test', true);
        $this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
    }

}
