<?php

namespace EcampCore\Controller;

class MailController extends AbstractBaseController
{

    public function userActivationAction()
    {
        $me = $this->getMe();
        $this->getUserService()->CreateActivationMail($me);

        die('Mail sent');
    }

}