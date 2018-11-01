<?php

namespace eCamp\Web\Controller\Camp;

use eCamp\Lib\Auth\AuthRequiredException;
use eCamp\Web\Controller\AbstractBaseController;
use Zend\View\Model\ViewModel;

class CampController extends AbstractBaseController {

    /**
     * @return array|ViewModel
     * @throws AuthRequiredException
     */
    public function indexAction() {
        $this->forceLogin();

        return [
            'camp' => $this->params()->fromRoute('camp'),
            'campId' => $this->params()->fromRoute('camp')->getId(),
        ];
    }
}
