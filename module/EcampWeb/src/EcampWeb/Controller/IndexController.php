<?php

namespace EcampWeb\Controller;

use Zend\View\Model\ViewModel;

use EcampCore\Validation\ValidationException;

class IndexController
    extends BaseController
{

    public function indexAction()
    {
        /* @var $campRepo \EcampCore\Repository\CampRepository */
        $campRepo = $this->getServiceLocator()->get('EcampCore\Repository\Camp');
        var_dump( $campRepo->findOneBy(array())->getName() );

        var_dump( $this->getServiceLocator()->get('EcampCore\Service\Camp')->Get("2")->getName());

        die();

        return new ViewModel();
    }

    /**
     * Test of simple update form
     * Data is validated twice, one in controller and once in service
     */
    public function updateAction()
    {
        $campId = "2";

        $camp = $this->getServiceLocator()->get('EcampCore\Service\Camp')->Get($campId);
        if (is_null($camp)) {
            throw new \Exception("Camp not found");
        }

        $form = new \EcampWeb\Form\CampUpdateForm($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        $form->bind($camp);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ( $form->isValid() ) {
                // save data
                $camp = $this->getServiceLocator()->get('EcampCore\Service\Camp')->Update($campId, $this->getRequest()->getPost());
            }
        }

        return new ViewModel(array(
                'form' => $form
        ));
    }

    /**
     * Test of simple update form
     * Data is not validated in controller. Validation Error from service id displayed.
     */
    public function updatedirectAction()
    {
        $v = new ViewModel();

        $campId = "2";

        $camp = $this->getServiceLocator()->get('EcampCore\Service\Camp')->Get($campId);
        if (is_null($camp)) {
            throw new \Exception("Camp not found");
        }

        $form = new \EcampWeb\Form\CampUpdateForm($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
        $form->bind($camp);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            try {
                $camp = $this->getServiceLocator()->get('EcampCore\Service\Camp')->Update($campId, $this->getRequest()->getPost());
            } catch (ValidationException $e) {
                $v->setVariable('validationError', $e->getMessage());
            }
        }

        $v->setVariable('form',$form);

        return $v->setTemplate('ecamp-web/index/update.twig');

    }

}
