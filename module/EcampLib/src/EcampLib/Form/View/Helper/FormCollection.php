<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormCollection;

class FormCollection extends TwbBundleFormCollection
{
    public function render(\Zend\Form\ElementInterface $oElement)
    {
        return "<twb-form-collection>" .  parent::render($oElement) . "</twb-form-collection>";
    }

}
