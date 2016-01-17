<?php

namespace EcampWeb\View\Helper;

use EcampCore\Entity\Group;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

class GroupUrl extends AbstractHelper
{
    private $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    public function __invoke(Group $group, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        $url = $this->url;

        if(!array_key_exists('group', $params)){
            $params['group'] = $group;
        }

        return $url('web/group-prefix/name/default', $params, $options, $reuseMatchedParams);
    }
}
