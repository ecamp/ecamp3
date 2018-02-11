<?php

namespace eCamp\Web\Controller;

use eCamp\Lib\Auth\AuthRequiredException;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractBaseController
{

    /**
     * @return void|ViewModel
     * @throws AuthRequiredException
     */
    public function indexAction() {
        $this->forceLogin();

    }

}
