<?php

namespace EcampWeb\Form;

use Zend\Form\Fieldset;

abstract class AjaxBaseForm extends BaseForm
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setHydrator(new ClassMethodsHydrator());
    }

    public function add($elementOrFieldset, array $flags = array())
    {
        parent::add($elementOrFieldset, $flags);

        if ($elementOrFieldset instanceof Fieldset) {
            if ($elementOrFieldset->useAsBaseFieldset()) {
                $elementOrFieldset->setHydrator($this->getHydrator());
            }
        }
    }

    public function setRedirectAfterSuccess($url)
    {
        $this->setAttribute('data-redirect-after-success', $url);
    }
}
