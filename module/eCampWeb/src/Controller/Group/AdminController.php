<?php

namespace eCamp\Web\Controller\Group;

use eCamp\Web\Controller\AbstractBaseController;

class AdminController extends AbstractBaseController
{

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction() {
        $group = $this->params()->fromRoute('group');

        return [
            'group' => $group,
        ];
    }

}
