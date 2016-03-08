<?php

namespace EcampWeb\View\Helper;

use EcampCore\Entity\Camp;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

class CampUrl extends AbstractHelper
{
    private $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    public function __invoke(Camp $camp, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        $url = $this->url;

        if(!array_key_exists('camp', $params)){
            $params['camp'] = $camp;
        }

        return $url('web/camp/default', $params, $options, $reuseMatchedParams);
    }
}
