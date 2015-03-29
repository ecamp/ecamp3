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
            /** @var \DoctrineModule\Form\Element\ObjectSelect $eventTypeElement */
            $eventTypeElement = $this->get(self::STEP_CAMP_EVENT_CATEGORIES)->getTemplateElement()->get('eventType');
            $eventTypeElement->setOptions(array('find_method' => array(
                'name' => 'findByCampTypeId',
                'params' => array('campTypeId' => $campTypeId)
            )));
        }

        return $return;
    }

    public function setData($newData)
    {
        $result = parent::setData($newData);

        $campType = $newData[self::STEP_CAMP_DETAILS]['campType'];

        if (isset($campType)) {
            $data = $this->getWizardData();

            // TODO: Load Event-Types for campType
            $data[self::STEP_CAMP_EVENT_CATEGORIES] = array(
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

            // TODO: Load Default daily Jobs:
            $data[self::STEP_CAMP_JOBS] = array(
                array(
                    'name' => 'Tages-Chef'
                )
            );
        }

        return $result;
    }

    private function getCampTypeId()
    {
        $data = $this->getWizardData();

        return $data['camp-details']['campType'];
    }

}
