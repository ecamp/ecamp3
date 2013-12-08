<?php

namespace EcampWeb\Form\Period;

use EcampWeb\Form\BaseForm;
use EcampCore\Validation\Period\PeriodSizeFieldset;

class ResizePeriod extends BaseForm
{
    public function __construct()
    {
        parent::__construct('resize-period-form');

        // add camp Fieldset
        $periodSizeFieldset = new PeriodSizeFieldset();
        $periodSizeFieldset->get('start')->setAttribute('readonly', true);
        $this->add($periodSizeFieldset);

    }
}
