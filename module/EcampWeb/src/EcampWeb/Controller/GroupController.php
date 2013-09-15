<?php

namespace EcampWeb\Controller;

class GroupController
    extends BaseController
{

    /**
     * @return \EcampCore\Service\GroupService
     */
    private function getGroupService()
    {
        return $this->serviceLocator->get('EcampCore\Service\Group');
    }

    /**
     * @return \EcampCore\Repository\GroupRepository
     */
    private function getGroupRepository()
    {
        return $this->serviceLocator->get('EcampCore\Repository\Group');
    }

    public function indexAction()
    {
        $group = $this->getGroupRepository()->find($this->params('group'));

        return array('group' => $group);
    }

}
