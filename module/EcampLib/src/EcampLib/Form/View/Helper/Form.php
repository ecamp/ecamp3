<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleForm;

class Form extends TwbBundleForm
{
    public function render(\Zend\Form\FormInterface $oForm, $sFormLayout = self::LAYOUT_HORIZONTAL)
    {
        return "<twb-form>" . parent::render($oForm, $sFormLayout) . "</twb-form>";
    }

}
