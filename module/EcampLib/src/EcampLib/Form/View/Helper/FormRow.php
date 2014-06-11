<?php
/**
 * Created by PhpStorm.
 * User: pirminmattmann
 * Date: 01.05.14
 * Time: 16:23
 */

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormRow;

class FormRow extends TwbBundleFormRow
{

    public function render(\Zend\Form\ElementInterface $oElement)
    {
        return "<twb-form-row>" . parent::render($oElement) . "</twb-form-row>";
    }

}
