<?php

namespace EcampLib\Form;

use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Session\Container;

class WizardForm extends BaseForm
{
    const INFO_FIELDSET_NAME = 'wizard-info';

    const FIRST_STEP = '[first]';
    const LAST_STEP = '[last]';
    const NEXT_STEP = '[next]';
    const PREV_STEP = '[prev]';
    const COMPLETED = '[completed]';

    private $dataContainer = null;
    private $stepFieldsets = null;

    public function init()
    {
        parent::init();

        $this->add(array(
            'name' => self::INFO_FIELDSET_NAME,
            'type' => 'EcampLib\Form\WizardInfoFieldset',
        ));
    }

    public function getWizardData()
    {
        return $this->getDataContainer();
    }

    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag = FormInterface::VALUES_NORMALIZED);
        $data[self::INFO_FIELDSET_NAME] = $this->getInfoFieldset()->getData();

        return $data;
    }

    public function setData($newData)
    {
        if (isset($newData[self::INFO_FIELDSET_NAME])) {
            $this->setWizardState($newData[self::INFO_FIELDSET_NAME]);
        }

        $presentData = $this->getDataContainer();

        $currentStepName = $this->getStep()->getName();
        $presentData->{$currentStepName} = $newData[$currentStepName];

        return parent::setData($presentData);
    }

    public function getStepData($step = null, $default = null)
    {
        $presentData = $this->getDataContainer();
        $step = $step ?: $this->getStepName();

        return $presentData->{$step} ?: $default;
    }

    public function setStepData($step, $stepData)
    {
        $presentData = $this->getDataContainer();
        $presentData->{$step} = $stepData;

        parent::setData($presentData);
    }

    public function hasStepData($step = null)
    {
        $presentData = $this->getDataContainer();
        $step = $step ?: $this->getStepName();

        return ($presentData->{$step} != null);
    }

    public function isValid()
    {
        $currentStepName = $this->getStep()->getName();
        $this->setValidationGroup($currentStepName);

        return parent::isValid();
    }

    public function isComplete()
    {
        if ($this->getStepName() == self::COMPLETED) {
            $this->setValidationGroup(FormInterface::VALIDATE_ALL);

            return parent::isValid();
        }

        return false;
    }

    public function getStepName()
    {
        $stepName = $this->getInfoFieldset()->getStepName();

        if (!$stepName) {
            $stepName = $this->setStep(self::FIRST_STEP);
        }

        return $stepName;
    }

    /** @return \Zend\Form\Fieldset */
    public function getStep()
    {
        $stepName = $this->getStepName();

        return $this->getStepFieldset($stepName) ?: $this->getFirstStep();
    }

    public function setStep($stepName)
    {
        if ($stepName == self::FIRST_STEP) {
            $stepName = $this->getFirstStep()->getName();
        }

        if ($stepName == self::LAST_STEP) {
            $stepName = $this->getLastStep()->getName();
        }

        if ($stepName == self::PREV_STEP) {
            $stepName = $this->getPrevStep()->getName();
        }

        if ($stepName == self::NEXT_STEP) {
            if ($this->getLastStep()->getName() == $this->getInfoFieldset()->getStepName()) {
                $stepName = self::COMPLETED;
            } else {
                $stepName = $this->getNextStep()->getName();
            }
        }

        if ($stepName != self::COMPLETED && !$this->getStepFieldset($stepName)) {
            $stepName = $this->getFirstStep()->getName();
        }

        if ($stepName != self::COMPLETED && !$this->hasStepData($stepName)) {
            $stepData = $this->initializeStepData($stepName);
            if (!empty($stepData)) {  $this->setStepData($stepName, $stepData);   }
        }

        $this->getInfoFieldset()->setStepName($stepName);

        return $stepName;
    }

    protected function initializeStepData(/** @noinspection PhpUnusedParameterInspection */ $stepName)
    {
        return array();
    }

    /** @return \Zend\Form\Fieldset */
    public function getFirstStep()
    {
        $stepFieldsets = $this->getStepFieldsets();

        return reset($stepFieldsets);
    }

    /** @return \Zend\Form\Fieldset */
    public function getLastStep()
    {
        $stepFieldsets = $this->getStepFieldsets();

        return end($stepFieldsets);
    }

    /** @return \Zend\Form\Fieldset */
    public function getNextStep()
    {
        $stepName = $this->getInfoFieldset()->getStepName();

        if ($stepName) {
            $returnNext = false;
            /** @var \Zend\Form\Fieldset $fieldset */
            foreach ($this->getStepFieldsets() as $fieldset) {
                if ($returnNext) { return $fieldset; }
                $returnNext = ($fieldset->getName() == $stepName);
            }

            return $this->getLastStep();
        } else {
            return $this->getFirstStep();
        }
    }

    /** @return \Zend\Form\Fieldset */
    public function getPrevStep()
    {
        $stepName = $this->getInfoFieldset()->getStepName();

        if ($stepName) {
            $prevStep = $this->getLastStep();
            /** @var \Zend\Form\Fieldset $fieldset */
            foreach ($this->getStepFieldsets() as $fieldset) {
                if ($fieldset->getName() == $stepName) {
                    return $prevStep;
                }
                $prevStep = $fieldset;
            }
        }

        return $this->getFirstStep();
    }

    public function createForm()
    {
        $form = new Form($this->getName(), $this->getOptions());
        $form->setFormFactory($this->getFormFactory());
        $form->setAttributes($this->getAttributes());
        $form->add($this->getInfoFieldset());
        $form->add($this->getStep());

        return $form;
    }

    /** @return WizardInfoFieldset */
    private function getInfoFieldset()
    {
        return $this->get(self::INFO_FIELDSET_NAME);
    }

    private function getStepFieldsets()
    {
        if ($this->stepFieldsets == null) {
            $this->stepFieldsets = array_filter($this->getFieldsets(), function($fieldset){
                /** @var \Zend\Form\Fieldset $fieldset */

                return $fieldset->getName() !== self::INFO_FIELDSET_NAME;
            });
        }

        return $this->stepFieldsets;
    }

    /**
     * @param $name
     * @return WizardInfoFieldset
     */
    private function getStepFieldset($name)
    {
        $name = $name ?: self::FIRST_STEP;
        $stepFieldsets = $this->getStepFieldsets();

        if (isset($stepFieldsets[$name])) {
            return $stepFieldsets[$name];
        }

        return null;
    }

    private function setWizardState($data)
    {
        $this->getInfoFieldset()->setId($data['id']);
        $this->setStep($data['step']);
    }

    /** @return Container */
    private function getDataContainer()
    {
        $containerId = $this->getDataContainerId();

        if ($this->dataContainer == null) {
            $this->dataContainer = new Container($containerId);
        }

        if ($this->dataContainer->getName() != $containerId) {
            $this->dataContainer = new Container($containerId);
        }

        return $this->dataContainer;
    }

    private function getDataContainerId()
    {
        return 'Wizard_' . $this->getInfoFieldset()->getId();
    }
}
