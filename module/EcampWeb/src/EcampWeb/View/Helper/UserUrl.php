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

    public function __invoke(User $user, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        $url = $this->url;

        if(!array_key_exists('user', $params)){
            $params['user'] = $user;
        }

        return $url('web/user-prefix/name/default', $params, $options, $reuseMatchedParams);
    }
}
