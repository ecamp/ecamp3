<?php

namespace EcampWeb\View\Helper;

use EcampCore\Acl\Privilege;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\User;
use EcampCore\Repository\CampCollaborationRepository;
use EcampCore\Repository\UserRepository;
use EcampLib\Acl\Acl;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface;

class Collaboration extends \Zend\View\Helper\AbstractHelper
{
    /**
     * @var \EcampLib\Acl\Acl
     */
    private $acl;

    /**
     * @var \EcampCore\Repository\UserRepository
     */
    private $userRepository;

    private $collaborationRepository;

    private $renderer;

    public function __construct(
        Acl $acl,
        UserRepository $userRepository,
        CampCollaborationRepository $collaborationRepository,
        RendererInterface $renderer
    ) {
        $this->acl = $acl;
        $this->userRepository = $userRepository;
        $this->collaborationRepository = $collaborationRepository;
        $this->renderer = $renderer;
    }

    private function getMe()
    {
        return $this->userRepository->getMe();
    }

    public function __invoke($campOrCollaboration, $userOrSize = null, $size = null)
    {
        $me = $this->getMe();

        if ($campOrCollaboration instanceof Camp) {
            $camp = $campOrCollaboration;

            if ($userOrSize instanceof User) {
                $user = $userOrSize;
            } else {
                $user = $this->getMe();
                $size = $userOrSize ?: $size;
            }
            $collaboration = $this->collaborationRepository->findByCampAndUser($camp, $user);

        } elseif ($campOrCollaboration instanceof CampCollaboration) {
            $collaboration = $campOrCollaboration;
            $user = $collaboration->getUser();
            $camp = $collaboration->getCamp();
            $size = $userOrSize;

        } else {
            throw new \Exception("First argument must be Camp or CampCollaboration");
        }

        $viewModel = null;
        $size = $size ?: '';

        if ($collaboration == null) {
            $viewModel = $this->renderNoCollaboration($camp, $user, $me);

        } elseif ($collaboration->isRequest()) {
            $viewModel = $this->renderRequest($collaboration, $me);

        } elseif ($collaboration->isInvitation()) {
            $viewModel = $this->renderInvitation($collaboration, $me);

        } elseif ($collaboration->isEstablished()) {
            $viewModel = $this->renderEstablished($collaboration, $me);
        }

        if ($viewModel != null) {
            $viewModel->setVariable('size', $size);

            return $this->renderer->render($viewModel);
        }

        return "";
    }

    public function renderNoCollaboration(Camp $camp, User $user, User $me)
    {
        if ($user == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/request-collaboration');
            $viewModel->setVariable('me', $me);
            $viewModel->setVariable('camp', $camp);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $camp, Privilege::CAMP_CONFIGURE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/invite-user');
            $viewModel->setVariable('user', $user);
            $viewModel->setVariable('camp', $camp);

            return $viewModel;
        }

        return null;
    }

    public function renderRequest(CampCollaboration $collaboration, User $me)
    {
        if ($collaboration->getUser() == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/revoke-request');
            $viewModel->setVariable('collaboration', $collaboration);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $collaboration->getCamp(), Privilege::CAMP_CONFIGURE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/accept-reject-request');
            $viewModel->setVariable('collaboration', $collaboration);

            return $viewModel;
        }

        return null;
    }

    public function renderInvitation(CampCollaboration $collaboration, User $me)
    {
        if ($collaboration->getUser() == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/accept-reject-invitation');
            $viewModel->setVariable('collaboration', $collaboration);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $collaboration->getCamp(), Privilege::CAMP_CONFIGURE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/revoke-invitation');
            $viewModel->setVariable('collaboration', $collaboration);

            return $viewModel;
        }

        return null;
    }

    public function renderEstablished(CampCollaboration $collaboration, User $me)
    {
        if ($collaboration->getUser() == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/leave-camp');
            $viewModel->setVariable('collaboration', $collaboration);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $collaboration->getCamp(), Privilege::CAMP_CONFIGURE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/collaboration/kick-user');
            $viewModel->setVariable('collaboration', $collaboration);

            return $viewModel;
        }

        return null;
    }
}
