<?php

namespace eCamp\Web\Controller\User;

use eCamp\Lib\Auth\AuthRequiredException;
use eCamp\Web\Controller\AbstractBaseController;
use Zend\View\Model\ViewModel;

class FriendsController extends AbstractBaseController {

    /**
     * @return array|ViewModel
     * @throws AuthRequiredException
     */
    public function indexAction() {
        $this->forceLogin();

        $user = $this->params()->fromRoute('user');

        return [
            'user' => $user
        ];
    }
}
