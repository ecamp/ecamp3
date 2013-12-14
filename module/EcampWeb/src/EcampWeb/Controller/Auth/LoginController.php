<?php

namespace EcampWeb\Controller\Auth;

use EcampWeb\Controller\BaseController;
use EcampWeb\Form\Auth\LoginForm;

class LoginController extends BaseController
{

    public function indexAction()
    {
        return array('login' => new LoginForm());
    }

    public function loginAction()
    {
    }

    public function logoutActoin()
    {
    }

}
