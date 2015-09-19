<?php

namespace EcampLib\View\Helper;

use Zend\View\Helper\AbstractHelper;

class SetTextDomain extends AbstractHelper
{
    public function __invoke($textDomain)
    {
        /** @var \Zend\View\Renderer\PhpRenderer $view */
        $view = $this->getView();

        /** @var \Zend\I18n\View\Helper\Translate $translateHelper */
        $translateHelper = $view->getHelperPluginManager()->get('Translate');
        $translateHelper->setTranslatorTextDomain($textDomain);

        /** @var \Zend\I18n\View\Helper\TranslatePlural $translatePluralHelper */
        $translatePluralHelper = $view->getHelperPluginManager()->get('TranslatePlural');
        $translatePluralHelper->setTranslatorTextDomain($textDomain);
    }

}
