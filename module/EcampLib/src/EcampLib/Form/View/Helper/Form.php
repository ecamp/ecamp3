<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleForm;
use Zend\Form\FormInterface;

class Form extends TwbBundleForm
{
    public function render(FormInterface $oForm, $sFormLayout = self::LAYOUT_HORIZONTAL)
    {
        return "<twb-form>" . parent::render($oForm, $sFormLayout) . "</twb-form>";
    }

}
