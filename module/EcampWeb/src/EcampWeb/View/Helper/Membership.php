<?php

namespace EcampWeb\View\Helper;

use EcampCore\Acl\Privilege;
use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;
use EcampCore\Entity\User;
use EcampCore\Repository\GroupMembershipRepository;
use EcampCore\Repository\UserRepository;
use EcampLib\Acl\Acl;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface;

class Membership extends \Zend\View\Helper\AbstractHelper
{
    /**
     * @var \EcampLib\Acl\Acl
     */
    private $acl;

    /**
     * @var \EcampCore\Repository\UserRepository
     */
    private $userRepository;

    private $membershipRepository;

    private $renderer;

    public function __construct(
        Acl $acl,
        UserRepository $userRepository,
        GroupMembershipRepository $membershipRepository,
        RendererInterface $renderer
    ) {
        $this->acl = $acl;
        $this->userRepository = $userRepository;
        $this->membershipRepository = $membershipRepository;
        $this->renderer = $renderer;
    }

    private function getMe()
    {
        return $this->userRepository->getMe();
    }

    public function __invoke($groupOrMembership, $userOrSize = null, $size = null)
    {
        $me = $this->getMe();

        if ($groupOrMembership instanceof Group) {
            $group = $groupOrMembership;

            if ($userOrSize instanceof User) {
                $user = $userOrSize;
            } else {
                $user = $this->getMe();
                $size = $userOrSize ?: $size;
            }
            $membership = $this->membershipRepository->findByGroupAndUser($group, $user);

        } elseif ($groupOrMembership instanceof GroupMembership) {
            $membership = $groupOrMembership;
            $user = $membership->getUser();
            $group = $membership->getGroup();
            $size = $userOrSize;

        } else {
            throw new \Exception("Erstes Argument muss Group oder GroupMembership sein");
        }

        $viewModel = null;
        $size = $size ?: '';

        if ($membership == null) {
            $viewModel = $this->renderNoMembership($group, $user, $me);

        } elseif ($membership->isRequest()) {
            $viewModel = $this->renderRequest($membership, $me);

        } elseif ($membership->isInvitation()) {
            $viewModel = $this->renderInvitation($membership, $me);

        } elseif ($membership->isEstablished()) {
            $viewModel = $this->renderEstablished($membership, $me);
        }

        if ($viewModel != null) {
            $viewModel->setVariable('size', $size);

            return $this->renderer->render($viewModel);
        }

        return "";
    }

    public function renderNoMembership(Group $group, User $user, User $me)
    {
        if ($user == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/request-membership');
            $viewModel->setVariable('me', $me);
            $viewModel->setVariable('group', $group);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $group, Privilege::GROUP_ADMINISTRATE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/invite-user');
            $viewModel->setVariable('user', $user);
            $viewModel->setVariable('group', $group);

            return $viewModel;
        }

        return null;
    }

    public function renderRequest(GroupMembership $membership, User $me)
    {
        if ($membership->getUser() == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/revoke-request');
            $viewModel->setVariable('membership', $membership);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $membership->getGroup(), Privilege::GROUP_ADMINISTRATE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/accept-reject-request');
            $viewModel->setVariable('membership', $membership);

            return $viewModel;
        }

        return null;
    }

    public function renderInvitation(GroupMembership $membership, User $me)
    {
        if ($membership->getUser() == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/accept-reject-invitation');
            $viewModel->setVariable('membership', $membership);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $membership->getGroup(), Privilege::GROUP_ADMINISTRATE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/revoke-invitation');
            $viewModel->setVariable('membership', $membership);

            return $viewModel;
        }

        return null;
    }

    public function renderEstablished(GroupMembership $membership, User $me)
    {

        if ($membership->getUser() == $me) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/leave-group');
            $viewModel->setVariable('membership', $membership);

            return $viewModel;

        } elseif ($this->acl->isAllowed($me, $membership->getGroup(), Privilege::GROUP_ADMINISTRATE)) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('ecamp-web/helper/membership/kick-user');
            $viewModel->setVariable('membership', $membership);

            return $viewModel;
        }

        return null;
    }
}
