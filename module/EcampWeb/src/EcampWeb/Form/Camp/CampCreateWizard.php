<?php

namespace EcampWeb\Form\Camp;

use EcampLib\Form\WizardForm;

class CampCreateWizard extends WizardForm
{

    const STEP_CAMP_DETAILS = 'camp-details';
    const STEP_CAMP_PERIODS = 'camp-periods';
    const STEP_CAMP_EVENT_CATEGORIES = 'camp-event-categories';
    const STEP_CAMP_JOBS = 'camp-jobs';

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name ?: 'create-camp', $options);
    }

    public function init()
    {
        parent::init();

        $this->add(array(
            'name' => self::STEP_CAMP_DETAILS,
            'type' => 'EcampCore\Fieldset\Camp\CampCreateFieldset'
        ));

        $this->add(array(
            'name' => self::STEP_CAMP_PERIODS,
            'type' => 'EcampWeb\Fieldset\Period\PeriodsCollection'
        ));

        $this->add(array(
            'name' => self::STEP_CAMP_EVENT_CATEGORIES,
            'type' => 'EcampWeb\Fieldset\EventCategory\EventCategoriesCollection'
        ));

        $this->add(array(
            'name' => self::STEP_CAMP_JOBS,
            'type' => 'EcampWeb\Fieldset\Job\JobsCollection'
        ));
    }

    public function setStep($stepName)
    {
        $return = parent::setStep($stepName);

        if ($campTypeId = $this->getCampTypeId()) {
            $this->get(self::STEP_CAMP_EVENT_CATEGORIES)->getTemplateElement()->setCampTypeId($campTypeId);
        }

        return $return;
    }

    protected function initializeStepData($stepName)
    {
        switch($stepName)
        {
            case self::STEP_CAMP_EVENT_CATEGORIES:
                $campTypeId = $this->getCampTypeId();

                return array(
                    array(
                        'name' => 'Lagersport',
                        'short' => 'LS',
                        'eventType' => '1235',
                        'numberingStyle' => '1',
                        'color' => '#55ff55'
                    ),
                    array(
                        'name' => 'Lageraktivität',
                        'short' => 'LA',
                        'eventType' => '1235',
                        'numberingStyle' => 'a',
                        'color' => '#ff5555'
                    )
                );

            case self::STEP_CAMP_JOBS:
                return array(
                    array(
                        'name' => 'Tages-Chef'
                    )
                );

            default:
                return array();
        }
    }

    private function getCampTypeId()
    {
        $data = $this->getStepData(self::STEP_CAMP_DETAILS);
        return $data['campType'];
    }

}
