<?php

namespace EcampCore\Controller;

use EcampCore\RepositoryUtil\RepositoryConfigWriter;

use EcampCore\ServiceUtil\ServiceConfigWriter;
use EcampCore\ServiceUtil\ServiceProviderWriter;
use EcampCore\RepositoryUtil\RepositoryTraitWriter;

use EcampLib\Controller\AbstractBaseController;

use EcampCore\Repository\GroupRepository;
use EcampCore\Repository\CampRepository;
use EcampCore\Repository\UserRepository;

class IndexController extends AbstractBaseController
{
    /** @var GroupRepository */
    private $groupRepo;

    /** @var CampRepository */
    private $campRepo;

    /** @var UserRepository */
    private $userRepo;

    public function onDispatch( \Zend\Mvc\MvcEvent $e )
    {
        $this->groupRepo =  $this->getServiceLocator()->get('EcampCore\Repository\Group');
        $this->campRepo = $this->getServiceLocator()->get('EcampCore\Repository\Camp');
        $this->userRepo = $this->getServiceLocator()->get('EcampCore\Repository\User');

        return parent::onDispatch( $e );
    }

    public function indexAction()
    {
        $groupId = $this->params('group');
        $campId = $this->params('camp');
        $userId = $this->params('user');

        if ($groupId) {
            $group = $this->groupRepo->find($groupId);
        }

        if ($campId) {
            $camp = $this->campRepo->find($campId);
        }

        if ($userId) {
            $user = $this->userRepo->find($userId);
        }

        return array('group' => $group, 'camp' => $camp, 'user' => $user);
    }

    public function createServiceConfigAction()
    {
        $serviceConfigWriter = new ServiceConfigWriter($this->getServiceLocator());
        $serviceConfigWriter->writeServiceConfigs();

        return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
    }

    public function createServiceProvidersAction()
    {
        $serviceProviderWriter = new ServiceProviderWriter($this->getServiceLocator());
        $serviceProviderWriter->writeServiceProviderInterfaces();

        return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
    }

    public function createRepoConfigAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $repoConfigWriter = new RepositoryConfigWriter($this->getServiceLocator(), $em);

        $repoConfigWriter->writeRepositoryConfigs();

        return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
    }

    public function createRepoProvidersAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $repoTraitWriter = new RepositoryTraitWriter($this->getServiceLocator(), $em);

        $repoTraitWriter->writeRepositoryTraits();

        return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
    }

    public function phpinfoAction()
    {
        phpinfo();
        die();
    }
}
