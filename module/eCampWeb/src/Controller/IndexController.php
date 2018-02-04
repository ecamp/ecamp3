<?php

namespace eCamp\Web\Controller;

use eCamp\Lib\Auth\AuthRequiredException;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractBaseController
{

    public function indexAction() {
        $this->forceLogin();

    }

}
