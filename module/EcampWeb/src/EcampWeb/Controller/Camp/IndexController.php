<?php

namespace EcampWeb\Controller\Camp;

use EcampCore\Validation\ValidationException;

use EcampCore\Entity\CampCollaboration;
class IndexController
    extends BaseController
{
    /**
     * @return \EcampCore\Service\PeriodService
     */
    private function getPeriodService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\period');
    }

    /**
     * @return \EcampCore\Repository\CampCollaborationRepository
     */
    private function getCollaborationRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\CampCollaboration');
    }

    /**
     * @return \EcampCore\Service\CampCollaborationService
     */
    private function getCollaborationService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\CampCollaboration');
    }

    public function indexAction()
    {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headScript()->appendFile($this->getRequest()->getBasePath() . '/js/ng-app/paginator.js');
        $renderer->headScript()->appendFile($this->getRequest()->getBasePath() . '/js/ajax-form.js');

        $myCollaboration = $this->getCollaborationRepository()->findByCampAndUser($this->getCamp(), $this->getMe());

        return array(
            'myCollaboration' => $myCollaboration
        );
    }

    public function requestCollaborationAction()
    {
        $role = $this->params()->fromQuery('role') ?: CampCollaboration::ROLE_MEMBER;
        $this->getCollaborationService()->requestCollaboration($this->getMe(), $this->getCamp(), $role);

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function revokeRequestAction()
    {
        $this->getCollaborationService()->revokeRequest($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function acceptInvitationAction()
    {
        $this->getCollaborationService()->acceptInvitation($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function rejectInvitationAction()
    {
        $this->getCollaborationService()->rejectInvitation($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function leaveCampAction()
    {
        $this->getCollaborationService()->leaveCamp($this->getMe(), $this->getCamp());

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp(), 'controller'=>'Index', 'action'=>'index')
        );
    }

    public function addPeriodAction()
    {
        $form = new \EcampWeb\Form\PeriodForm();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ( $form->isValid() ) {
                // save data
                try {
                    $period = $this->getPeriodService()->Create($this->getCamp(), $this->getRequest()->getPost());

                } catch (ValidationException $e) {
                    $error = $e->getMessageArray();
                    if( $error['data'] && is_array( $error['data']) )
                        $form->setMessages($error['data']);
                    else
                        $form->setFormError($error);

                    $this->getResponse()->setStatusCode(500);
                }
            } else {
                $this->getResponse()->setStatusCode(500);
            }
        }

        return array('form' => $form);
    }

}
