<?php

namespace EcampWeb\View\Helper;

use Zend\View\Helper\Url;

class BaseUrl extends Url
{
    public function __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        // web/camp/* 	wird zu:
        // web/group-prefix/name+camp/*
        // web/user-prefix/name+camp/*

        if (substr($name, 0, 8) == 'web/camp') {
            /* @var $camp \EcampCore\Entity\Camp */
            $camp = $params['camp'];

            if ($camp->belongsToUser()) {
                $params['user'] = $camp->getOwner();
                $name = 'web/user-prefix/name+camp' . substr($name, 8);
            } else {
                $params['group'] = $camp->getOwner();
                $name = 'web/group-prefix/name+camp' . substr($name, 8);
            }
        }

        return parent::__invoke($name, $params, $options, $reuseMatchedParams);
    }
}
