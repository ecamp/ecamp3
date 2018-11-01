<?php

namespace eCamp\Web\Controller\Group;

use eCamp\Web\Controller\AbstractBaseController;

class GroupController extends AbstractBaseController {
    public function indexAction() {
        return [
            'group' => $this->params()->fromRoute('group')
        ];
    }
}
