<?php

namespace EcampWeb\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\Url;

class CampUrl extends Url
{
    public function fromRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        // web/camp/* 	wird zu:
        // web/group-prefix/name+camp/*
        // web/user-prefix/name+camp/*

        if (substr($route, 0, 8) == 'web/camp') {
            /* @var $camp \EcampCore\Entity\Camp */
            $camp = $params['camp'];

            if ($camp->belongsToUser()) {
                $params['user'] = $camp->getOwner();
                $route = 'web/user-prefix/name+camp' . substr($route, 8);
            } else {
                $params['group'] = $camp->getGroup();
                $route = 'web/group-prefix/name+camp' . substr($route, 8);
            }
        }

        return parent::fromRoute($route, $params, $options, $reuseMatchedParams);
    }
}
