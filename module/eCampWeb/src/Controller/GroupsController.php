<?php

namespace eCamp\Web\Controller;

use eCamp\Core\Repository\GroupRepository;
use eCamp\Core\EntityService\GroupService;
use Zend\View\Model\ViewModel;

class GroupsController extends AbstractBaseController
{
    /** @var GroupService */
    private $groupService;

    /**
     * @param GroupService $groupService
     */
    public function __construct(
        GroupService $groupService
    ) {
        $this->groupService = $groupService;
    }


    public function indexAction()
    {
        $userGroups = $this->groupService->fetchByUser();
        $rootGroups = $this->groupService->fetchByParentGroup(null);

        return [
            'userGroups' => $userGroups,
            'rootGroups' => $rootGroups
        ];
    }
}
