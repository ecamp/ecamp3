<?php
namespace EcampCore\Validation;

use EcampCore\Entity\Camp;
use EcampCore\Entity\Event;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class EventFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(Camp $camp, Event $event = null)
    {
        parent::__construct('event');

        $valueOptions = array();

        $eventCategories = $camp->getEventCategories();
        foreach ($eventCategories as $ec) {
            $valueOptions[$ec->getId()] = array(
                'label' => $ec->getName(),
                'options' => array(),
            );

            $valueOptions[$ec->getId()]['options'][0] = array(
                'value' => $ec->getId(),
                'label' => $ec->getName() . ' - Standard'
            );

            $eventTypeFactories = $ec->getEventType()->getEventTypeFactories();
            foreach ($eventTypeFactories as $etf) {
                $valueOptions[$ec->getId()]['options'][$etf->getId()] = array(
                    'value' => $ec->getId() . ' - ' . $etf->getId(),
                    'label' => $ec->getName() . ' - ' . $etf->getName()
                );
            }

        }

        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => 'Eventname'
            ),
            'type' => 'Text',
        ));

        $this->add(array(
            'name' => 'eventCategory',
            'type' => 'Select',
            'options' => array(
                'label' => 'Event Category',
                'value_options' => $valueOptions
            ),
            'attributes' => array(
                'id' => 'eventEventCategory',
                'class' => 'selectpicker',
            )
        ));

        if ($event != null) {
            $this->get('title')->setValue($event->getTitle());
            $this->get('eventCategory')->setValue($event->getEventCategory());
        }

    }

    public function getInputFilterSpecification()
    {
        return array(
            array(
                'name' => 'title',
                'required' => true,
            ),
            array(
                'name' => 'eventCategory',
                'required' => false,
            )
        );
    }
}
