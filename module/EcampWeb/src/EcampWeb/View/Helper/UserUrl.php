<?php

namespace EcampWeb\View\Helper;

use EcampCore\Entity\User;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

class UserUrl extends AbstractHelper
{
    private $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    public function __invoke(User $user)
    {
        $url = $this->url;

        return $url('web/user-prefix/name', array('user' => $user));
    }
}
