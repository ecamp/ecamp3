<?php

namespace EcampCore\Service;

use EcampCore\Entity\Group;
use EcampCore\Entity\GroupRequest;

use EcampLib\Service\ServiceBase;
use EcampLib\Service\Params\Params;
use EcampCore\Repository\GroupRepository;
use EcampCore\Acl\Privilege;
use EcampLib\Validation\ValidationException;

class GroupService
    extends ServiceBase
{
    /** @var GroupRepository */
    private $groupRepo;

    public function __construct(

        GroupRepository $groupRepo
    ){
        $this->groupRepo = $groupRepo;
    }

    /**
     * Returns the Group with the given ID
     *
     * If no Identifier is given, the Context Group is returned
     *
     * @return \EcampCore\Entity\Group
     */
    public function Get($id)
    {
        $group = null;

        if (is_string($id)) {
            $group = $this->groupRepo->find($id);
        }

        if ($id instanceof Group) {
            $group = $id;
        }

        return
            $group != null && $this->aclIsAllowed($group, Privilege::GROUP_LIST) ?
            $group :
            null;
    }

    /**
     * Return all root groups
     * @return
     */
    public function GetRoots()
    {
        $rootGroups = $this->groupRepo->findRootGroups();

        return array_filter($rootGroups, array($this, 'filterGroups'));
    }

    /**
     * Updates the current Group
     *
     * @return \EcampCore\Entity\Group
     */
    public function Update($groupId, $data)
    {
        $group = $this->Get($groupId);
        $this->aclRequire($group, Privilege::GROUP_ADMINISTRATE);

        $groupValidationForm = $this->createValidationForm(
            $group, $data, array_intersect(array_keys($data), array('description')));

        if (!$groupValidationForm->isValid()) {
            throw ValidationException::FromForm($groupValidationForm);
        }

        return $group;
    }

    private function filterGroups(Group $group)
    {
        return $this->aclIsAllowed($group, Privilege::GROUP_LIST);
    }

    /**
     * Request a new Group
     * @return EcampCore\Entity\GroupRequest
     */
    public function RequestGroup(Params $params)
    {
        /* grab parent_group from context */
        $group = $this->getContextProvider()->getGroup();

        $new_groupname = $params->getValue("name");
        $me = $this->getContextProvider()->getMe();

        /* check if group name is unique in parent_group */
        foreach ($group->getChildren() as $subgroup) {
            if ($subgroup->getName() == $new_groupname) {
                $params->addError('name', "Group with same name already exists.");
                $this->validationFailed();
            }
        }

        // Neue GroupRequest erstellen
        $groupRequest = new GroupRequest();
        // Daten, welche nicht über die $form definiert werden, müssen von Hand gesetzt werden:
        $groupRequest->setRequester($me)->setParent($group);

        // GroupValidator erstellen:
        $grouprequestValidator = new \EcampCore\Validator\Entity\GroupRequestValidator($groupRequest);

        // Die gemachten Angaben in der $form gegen die neue $groupRequest validieren
        if ($grouprequestValidator->isValid($params)) {

            // und auf die GroupRequest anwenden, wenn diese gültig sind.
            $grouprequestValidator->apply($params);

            // die neue und gültie GroupRequest persistieren.
            $this->persist($groupRequest);
        } else {
            // Wenn die Validierung fehl schlägt, muss dies festgehalten werden:
            $this->validationFailed();
        }
    }

}
