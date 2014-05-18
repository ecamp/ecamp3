<?php

namespace EcampCore\Controller;

use Zend\Json\Json;
use Zend\Http\PhpEnvironment\Response;

class FormValidationController extends AbstractBaseController
{
    public function validateAction()
    {
        try {
            $formName = $this->params()->fromQuery('formName');
            $formInputs = $this->params()->fromPost();

            $fieldNames = $this->array_keys_recursive($formInputs);

            /* @var $form \Zend\Form\Form */
            $form = $this->getFormElementManager()->get($formName);
            $form->setValidationGroup($fieldNames);

            $form->setData($formInputs);

            if ($form->isValid()) {
                return $this->emptyResponse();

            } else {
                $resp = $this->emptyResponse(Response::STATUS_CODE_500);
                $resp->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8');
                $resp->getHeaders()->addHeaderLine('Keep-Alive', 'timeout=5, max=100');
                $resp->setContent(Json::encode($form->getMessages()));

                return $resp;
            }

        } catch (\Exception $ex) {
            $resp = $this->emptyResponse(Response::STATUS_CODE_500);
            $resp->setContent($ex->getMessage());

            return $resp;
        }
    }

    private function array_keys_recursive(array $array)
    {
        $result = array();
        $keys = array_keys($array);

        foreach ($keys as $key) {
            if (is_array($array[$key])) {
                $result[$key] = $this->array_keys_recursive($array[$key]);
            } else {
                $result[] = $key;
            }
        }

        return $result;
    }

}
