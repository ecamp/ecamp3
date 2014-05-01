<?php

namespace EcampCore\Controller;

use EcampCore\Repository\UserRepository;
use EcampCore\Repository\CampRepository;
use EcampLib\Controller\AbstractBaseController;

use Zend\View\Model\ViewModel;

class TestController extends AbstractBaseController
{

    /** @var UserRepository */
    private $userRepo;

    /** @var CampRepository */
    private $campRepo;

    public function testAction()
    {
        echo get_class($this->campRepo);
        echo "<br />";
    }

    public function indexAction()
    {
        $this->userRepo = $this->getServiceLocator()->get('EcampCore\Repository\User');
        $this->campRepo = $this->getServiceLocator()->get('EcampCore\Repository\Camp');

        $user = $this->userRepo->find('2de20f49');
        $users = array($user);
        $camps = array($this->campRepo->find('cc1'));

        foreach ($camps as $camp) {
            echo "Camp " . $camp->getId() . ":" . PHP_EOL;

            foreach ($users as $user) {
                echo "    User " . $user->getId() . ": ";
                echo $camp->isMember($user) ? "1" : "0";
                echo PHP_EOL;
            }
            echo PHP_EOL;
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate("ecamp-core/index/index");

        return $viewModel;
    }

    public function formAction()
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');

        return array(
            'form' => $formElementManager->get('EcampCore\Controller\TestForm')
        );

    }

}
