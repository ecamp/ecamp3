<?php

namespace eCamp\Web\Controller;

class GroupController extends AbstractBaseController
{

    public function indexAction() {
        return [
            'group' => $this->params()->fromRoute('group')
        ];
    }

}
