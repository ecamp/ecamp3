<?php

namespace EcampWeb\Form\Job;

use EcampWeb\Form\AjaxBaseForm;

class JobForm extends AjaxBaseForm
{
    public function __construct($name = null)
    {
        $name = $name ?: 'job';
        parent::__construct($name);
    }

    public function init()
    {
        parent::init();

        $this->add(array(
            'name' => 'job',
            'type' => 'EcampCore\Fieldset\Job\JobFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));
    }

}
