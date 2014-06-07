<?php

namespace EcampWeb\Controller;

use EcampLib\Validation\ValidationException;

class CampController extends BaseController
{
    public function createAction()
    {
        $form = $this->createForm('EcampWeb\Form\Camp\CampCreateForm');
        $form->setAction($this->url()->fromRoute('web/default', array('controller'=> 'Camp', 'action' => 'create')));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $camp = $this->getCampService()->Create($data);

                    return $this->getRedirectResponse(
                        $this->url()->fromRoute('web/camp/default', array('camp' => $camp, 'controller' => 'index'))
                    );

                } catch (ValidationException $ex) {
                    $form->setMessages($ex->getValidationMessages());
                }
            } else {
            }
        }

        return array('form' => $form);
    }

}
