<?php

namespace EcampApi\Resource\Collaboration;

use EcampCore\Entity\Camp;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\User;
use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

abstract class CollaborationBaseResource extends HalResource
{
    /** @var \EcampCore\Entity\CampCollaboration */
    protected $collaboration;

    /** @var \EcampCore\Entity\Camp */
    protected $camp;

    /** @var \EcampCore\Entity\User */
    protected $user;

    public function __construct($id, CampCollaboration $collaboration = null, Camp $camp = null, User $user = null)
    {
        $this->collaboration = $collaboration;

        if ($collaboration != null) {
            $this->camp = $collaboration->getCamp();
            $this->user = $collaboration->getUser();
        } else {
            $this->camp = $camp;
            $this->user = $user;
        }

        $object = $this->createObject();
        parent::__construct($object, $id);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/camps/collaborators', array(
            'camp' => $this->camp->getId(),
            'collaborator' => $this->user->getId()
        ));
        $this->getLinks()->add($selfLink);

        if ($collaboration != null) {
            $eventRespLink = new Link('event_resps');
            $eventRespLink->setRoute('api/collaborations/event_resps', array('collaboration' => $collaboration->getId()));
            $this->getLinks()->add($eventRespLink);
        }
    }

    abstract protected function createObject();

    public function setVisitor(User $visitor)
    {
        $description = "";

        if ($this->collaboration == null) {
            if ($this->user->getId() == $visitor->getId()) {
                $description = "You are not a collaborator of {{ campName }}.";
                $this->addActionLink('request');
            } elseif ($this->camp->campCollaboration()->isManager($visitor)) {
                $description = "{{ userName }} is not a collaborator of {{ campName }}.";
                $this->addActionLink('invite');
            }
        } else {
            if ($this->collaboration->isEstablished()) {
                if ($this->user->getId() == $visitor->getId()) {
                    $description = "You are {{ role }} of {{ campName }}.";
                    $this->addActionLink('leave');
                } elseif ($this->camp->campCollaboration()->isManager($visitor)) {
                    $description = "Kick {{ userName }} out of {{ campName }}.";
                    $this->addActionLink('kick');
                }

            } elseif ($this->collaboration->isInvitation()) {
                if ($this->user->getId() == $visitor->getId()) {
                    $description = "You are invited to {{ campName }} as {{ role }}.";
                    $this->addActionLink('acceptInvitation');
                    $this->addActionLink('rejectInvitation');
                } elseif ($this->camp->campCollaboration()->isManager($visitor)) {
                    $description = "{{ userName }} has been invited (as {{ role }}) to {{ campName }}.";
                    $this->addActionLink('revokeInvitation');
                }

            } elseif ($this->collaboration->isRequest()) {
                if ($this->user->getId() == $visitor->getId()) {
                    $description = "You have requested collaboration (as {{ role }}) to {{ campName }}.";
                    $this->addActionLink('revokeRequest');
                } elseif ($this->camp->campCollaboration()->isManager($visitor)) {
                    $description = "{{ userName }} has requested collaboration (as {{ role }}) to {{ campName }}.";
                    $this->addActionLink('acceptRequest');
                    $this->addActionLink('rejectRequest');
                }

            }
        }

        $description = str_replace("{{ campName }}", $this->camp->getName(), $description);
        $description = str_replace("{{ userName }}", $this->user->getDisplayName(), $description);
        $description = str_replace("{{ role }}", $this->getRole(), $description);

        $this->resource['description'] = $description;
    }

    protected function addActionLink($action)
    {
        $link = new Link($action);
        $link->setRoute('api/camps/collaborators/action', array(
            'camp' => $this->camp->getId(),
            'collaborator' => $this->user->getId(),
            'action' => $action
        ));

        $this->getLinks()->add($link);
    }

    protected function getRole()
    {
        if ($this->collaboration == null) {
            return null;
        }

        return $this->collaboration->getRole();
    }

    protected function getStatus()
    {
        if ($this->collaboration == null) {
            return CampCollaboration::STATUS_UNRELATED;
        }

        return $this->collaboration->getStatus();
    }
}
