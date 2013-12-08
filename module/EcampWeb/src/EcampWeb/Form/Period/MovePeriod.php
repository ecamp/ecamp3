<?php

namespace EcampWeb\Form\Period;

use EcampWeb\Form\BaseForm;
use EcampCore\Validation\Period\PeriodMoveFieldset;

class MovePeriod extends BaseForm
{
    public function __construct()
    {
        parent::__construct('move-period-form');

        $periodMoveFieldset = new PeriodMoveFieldset();
        $this->add($periodMoveFieldset);

        $this->setAttribute('data-async', null);
        $this->setAttribute('data-target', '#asyncform-container');
    }
}
