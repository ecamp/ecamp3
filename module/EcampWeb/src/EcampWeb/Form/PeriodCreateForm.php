<?php

namespace EcampWeb\Form;

use Zend\Form\Form;
use EcampCore\Validation\PeriodFieldset;

class PeriodCreateForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct('period-form');

        // add period fieldset
        $periodFieldset = new PeriodFieldset(null);
        $periodFieldset->setUseAsBaseFieldset(true);
        $this->add($periodFieldset);
    }
}
