<?php

namespace EcampWeb\Form;

use Zend\Form\Fieldset;
class AjaxBaseForm extends BaseForm
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options = array());

        $this->setAttribute('data-async', null);
        $this->setAttribute('data-target', '#asyncform-container');

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
}
