<?php

namespace eCamp\Web\Controller;

use eCamp\Core\Repository\GroupRepository;
use Zend\View\Model\ViewModel;

class GroupsController extends AbstractBaseController
{
    private $groupRepository;

    /**
     * @param GroupRepository $groupRepository
     * @return void|ViewModel
     */
    public function __construct(GroupRepository $groupRepository) {
        $this->groupRepository = $groupRepository;
    }


    public function indexAction() {
        $groups = $this->groupRepository->findBy([
            'parent' => null
        ]);

        return [
            'groups' => $groups
        ];
    }

}
