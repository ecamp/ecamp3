<?php

namespace EcampWeb\Form;

use Zend\Form\Form;
use EcampCore\Validation\PeriodFieldset;

class PeriodForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct('period-form');

        // add camp Fieldset
        $periodFieldset = new PeriodFieldset(null);
        $periodFieldset->setUseAsBaseFieldset(true);
        $this->add($periodFieldset);

        $this->add(array(
                'name' => 'send',
                'type'  => 'Submit',
                'attributes' => array(
                        'value' => '+ Add Period',
                ),
        ));
    }
}
