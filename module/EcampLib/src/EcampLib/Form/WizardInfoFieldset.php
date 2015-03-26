<?php

namespace EcampLib\Form;

class WizardInfoFieldset extends BaseFieldset
{
    const FIRST_STEP = '[first]';

    public function init()
    {
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
        ));

        $this->add(array(
            'name' => 'step',
            'type' => 'hidden',
        ));

        $this->getIdElement()->setValue($this->createId());
    }

    public function getData()
    {
        return array(
            'id' => $this->getId(),
            'step' => $this->getStepName()
        );
    }

    private function getIdElement()
    {
        return $this->get('id');
    }

    public function getId()
    {
        return $this->getIdElement()->getValue();
    }

    public function setId($id)
    {
        $this->getIdElement()->setValue($id);
    }

    private function createId()
    {
        return md5(microtime(true) . "#" . mt_rand());
    }

    private function getStepElement()
    {
        return $this->get('step');
    }

    public function getStepName()
    {
        return $this->getStepElement()->getValue();
    }

    public function setStepName($step)
    {
        $this->getStepElement()->setValue($step);
    }

}
