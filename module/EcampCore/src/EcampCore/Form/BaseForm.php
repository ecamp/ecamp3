<?php

namespace EcampCore\Form;

use Zend\Form\Form;

class BaseForm extends Form
{
    private $formManagerKey;

    public function __construct($name = null, $options = array(), $formManagerKey = null)
    {
        parent::__construct($name, $options);

        $this->formManagerKey = ($formManagerKey != null)
            ? $formManagerKey
            : (isset($options['form_manager_key']) ? $options['form_manager_key'] : get_class($this));

        $this->setAttribute('data-form-manager-key', $this->formManagerKey);
    }

    protected function getFormManagerKey()
    {
        return $this->formManagerKey;
    }

}
