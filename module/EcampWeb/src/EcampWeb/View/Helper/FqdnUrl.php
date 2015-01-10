<?php

namespace EcampWeb\View\Helper;

class FqdnUrl extends BaseUrl
{
    public function __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        return __DOMAIN__ . parent::__invoke($name, $params, $options, $reuseMatchedParams);
    }
}