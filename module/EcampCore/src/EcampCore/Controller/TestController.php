<?php

namespace EcampCore\Controller;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
//use Zend\Form\Annotation\AnnotationBuilder;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use EcampCore\Repository\UserRepository;
use EcampCore\Repository\CampRepository;

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

    public function ownerAction()
    {
        $campOwnerRepo = $this->getServiceLocator()->get('EcampCore\Repository\AbstractCampOwner');
        $campOwners = $campOwnerRepo->findPossibleCampOwner();

        foreach ($campOwners as $campOwner) {
            echo $campOwner;
        }

        die();

    }

    public function buildAnnotationAction()
    {
/*
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $builder = new AnnotationBuilder($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));

        $camp = $this->getServiceLocator()->get('EcampCore\Repository\Camp')->find(2);

        $form = $builder->createForm('EcampCore\Entity\Camp');
        $form->setHydrator(new DoctrineObject($em));
        $form->bind($camp);

        $form->setValidationGroup('name');
        $form->setData(array('name' => '', 'title' => ''));

        echo $form->isValid() ? 'valid' : 'notvalid';
        echo "   -   ";
        echo $camp->getName();
        echo "   -   ";
        echo $camp->getTitle();
*/
//        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
//        $builder = new AnnotationBuilder($em);
//
//        $form = $builder->createForm('EcampCore\Entity\Camp');

        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form = $formElementManager->get('EcampWeb\Form\Camp\CampCreateForm');
//        $form = $formElementManager->get('EcampCore\Entity\CampInterface');

        $form->setData(array(
            'name' => 'myCampName',
            'title' => 'myCampTitle',
            'motto' => 'myCampMotto',
            'security' => '1234'
        ));
        $form->isValid();

        return array('form' => $form);

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

    public function cliJobAction()
    {
        $token = (new \EcampCore\Job\Zf2CliJob('modules list'))->enqueue();

        die('Job created, Token: ' . $token);
    }

    public function eventPrinterAction()
    {
        $eventRepo = $this->getServiceLocator()->get('EcampCore\Repository\Event');
        $events = $eventRepo->findBy(array());
        $event = array_pop($events);

        $token = (new \EcampCore\Job\EventPrinter($event))->enqueue();

        die('Job created, Token: ' . $token);
    }
}
