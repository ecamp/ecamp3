<?php

namespace EcampLib\Controller;

use EcampLib\Form\BaseForm;
use EcampWeb\Form\ClassMethodsHydrator;
use Zend\Form\FormElementManager;
use Zend\Http\Response;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractBaseController extends AbstractActionController
{

    /**
     * @return FormElementManager
     */
    protected function getFormElementManager()
    {
        return $this->getServiceLocator()->get('FormElementManager');
    }

    /**
     * @param $formName
     * @return BaseForm
     */
    protected function createForm($formName)
    {
        /* @var $form BaseForm */
        $form = $this->getFormElementManager()->get($formName);
        $form->setHydrator(new ClassMethodsHydrator(false));

        return $form;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->getServiceLocator()->get('Logger');
    }

    /**
     * @param  integer  $statusCode
     * @return Response
     */
    protected function emptyResponse($statusCode = Response::STATUS_CODE_204)
    {
        /* @var $response Response */
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        return $response;
    }

}
