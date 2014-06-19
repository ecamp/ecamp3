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

    public function __invoke(Group $group)
    {
        $url = $this->url;

        return $url('web/group-prefix/name', array('group' => $group));
    }
}
