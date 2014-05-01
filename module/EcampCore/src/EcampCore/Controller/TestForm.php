<?php

namespace EcampCore\Controller;

use EcampWeb\Form\BaseForm;

class TestForm extends \EcampCore\Form\BaseForm
{
    public function __construct()
    {
        parent::__construct('test');

    }

    public function init()
    {
        parent::init();

        $day = new \EcampCore\Fieldset\DayFieldset('day', array('show' => true));
        $day->init();

        $this->add($day);

    }

}
