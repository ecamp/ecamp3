<?php

namespace EcampWeb\Form\Period;

use Zend\Form\Form;
use EcampCore\Validation\Period\PeriodSizeFieldset;
use EcampCore\Validation\Period\PeriodEditFieldset;
use EcampWeb\Form\BaseForm;

class CreatePeriod extends BaseForm
{
    public function __construct()
    {
        parent::__construct('create-period-form');

        // add camp Fieldset
        $periodSizeFieldset = new PeriodSizeFieldset();
        $this->add($periodSizeFieldset);

        $editPeriodFieldset = new PeriodEditFieldset();
        $this->add($editPeriodFieldset);
    }
}
