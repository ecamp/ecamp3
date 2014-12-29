<?php

namespace EcampApi\Resource\Membership;

use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;
use EcampCore\Entity\User;
use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

abstract class MembershipBaseResource extends HalResource
{
    /** @var \EcampCore\Entity\GroupMembership */
    protected $membership;

    /** @var \EcampCore\Entity\Group */
    protected $group;

    /** @var \EcampCore\Entity\User */
    protected $user;

    public function __construct($id, GroupMembership $membership = null, Group $group = null, User $user = null)
    {
        $this->membership = $membership;

        if ($membership != null) {
            $this->group = $membership->getGroup();
            $this->user = $membership->getUser();
        } else {
            $this->group = $group;
            $this->user = $user;
        }

        $object = $this->createObject();
        parent::__construct($object, $id);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/groups/members', array(
            'group' => $this->group->getId(),
            'member' => $this->user->getId()
        ));
        $this->getLinks()->add($selfLink);
    }

    abstract protected function createObject();

    public function setVisitor(User $visitor)
    {
        $description = "";

        if ($this->membership == null) {
            if ($this->user->getId() == $visitor->getId()) {
                $description = "You are not a member of {{ groupName }}."; // You can request your membership.";
                $this->addActionLink('request');
            } elseif ($this->group->groupMembership()->isManager($visitor)) {
                $description = "{{ userName }} is not a member of {{ groupName }}."; // You can invite {{ userName }}.";
                $this->addActionLink('invite');
            }

        } else {
            if ($this->membership->isEstablished()) {
                if ($this->user->getId() == $visitor->getId()) {
                    $description = "You are {{ role }} of {{ groupName }}."; // You can leave {{ groupName }}.";
                    $this->addActionLink('leave');
                } elseif ($this->group->groupMembership()->isManager($visitor)) {
                    $description = "Kick {{ userName }} out of {{ groupName }}."; // "You are Manager of {{ groupName }}."; // You can kick {{ userName }}.";
                    $this->addActionLink('kick');
                }

            } elseif ($this->membership->isInvitation()) {
                if ($this->user->getId() == $visitor->getId()) {
                    $description = "You are invited to {{ groupName }} as {{ role }}."; // You can accept or reject this invitation.";
                    $this->addActionLink('acceptInvitation');
                    $this->addActionLink('rejectInvitation');
                } elseif ($this->group->groupMembership()->isManager($visitor)) {
                    $description = "{{ userName }} has been invited (as {{ role }}) to {{ groupName }}."; // You can revoke this invitation.";
                    $this->addActionLink('revokeInvitation');
                }

            } elseif ($this->membership->isRequest()) {
                if ($this->user->getId() == $visitor->getId()) {
                    $description = "You have requested membership (as {{ role }}) to {{ groupName }}."; // You can revoke this request.";
                    $this->addActionLink('revokeRequest');
                } elseif ($this->group->groupMembership()->isManager($visitor)) {
                    $description = "{{ userName }} has requested membership (as {{ role }}) to {{ groupName }}."; // You can accept or reject this request.";
                    $this->addActionLink('acceptRequest');
                    $this->addActionLink('rejectRequest');
                }

            }
        }

        $description = str_replace("{{ groupName }}", $this->group->getName(), $description);
        $description = str_replace("{{ userName }}", $this->user->getDisplayName(), $description);
        $description = str_replace("{{ role }}", $this->getRole(), $description);

        $this->resource['description'] = $description;
    }

    protected function addActionLink($action)
    {
        $link = new Link($action);
        $link->setRoute('api/groups/members/action', array(
            'group' => $this->group->getId(),
            'member' => $this->user->getId(),
            'action' => $action
        ));

        $this->getLinks()->add($link);
    }

    protected function getRole()
    {
        if ($this->membership == null) {
            return null;
        }

        return $this->membership->getRole();
    }

    protected function getStatus()
    {
        if ($this->membership == null) {
            return GroupMembership::STATUS_UNRELATED;
        }

        return $this->membership->getStatus();
    }
}
