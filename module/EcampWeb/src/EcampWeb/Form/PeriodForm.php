<?php

namespace EcampWeb\Form;

use Zend\Form\Form;
use EcampCore\Validation\PeriodFieldset;

class PeriodForm extends BaseForm
{
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        // add camp Fieldset
        $periodFieldset = new PeriodFieldset($entityManager);
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
