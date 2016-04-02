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

        if (!isset($params['locale'])) {
            /** @var \Zend\View\Renderer\PhpRenderer $view */
            $view = $this->getView();

            /** @var \Zend\I18n\View\Helper\Translate $translateHelper */
            $translateHelper = $view->getHelperPluginManager()->get('Translate');

            /** @var \Zend\I18n\Translator\Translator $translator */
            $translator = $translateHelper->getTranslator();

            if($translator) {
                $params['locale'] = $translator->getLocale();
            }
        }

        return parent::__invoke($name, $params, $options, $reuseMatchedParams);
    }
}
