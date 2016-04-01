<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormRow;

class FormRow extends TwbBundleFormRow
{

    public function render(\Zend\Form\ElementInterface $oElement, $labelPosition = null)
    {
        return "<twb-form-row>" . parent::render($oElement, $labelPosition) . "</twb-form-row>";
    }

}
