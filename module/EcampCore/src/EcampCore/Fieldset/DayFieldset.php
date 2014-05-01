<?php

namespace EcampCore\Fieldset;

use EcampCore\Entity\Day;
use EcampLib\Form\BaseFieldset;

class DayFieldset extends BaseFieldset
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name ?: 'day', $options);
    }

    protected $showStory = false;

    protected $showJobs = false;

    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['binding_entity'])) {
            $this->setBindingEntity($options['binding_entity']);
        }

        if (isset($options['show_story'])) {
            $this->showStory = $options['show_story'];
        }

        if (isset($options['show_jobs'])) {
            $this->showJobs = $options['show_jobs'];
        }
    }

    /**
     * @param Day $day
     */
    public function setBindingEntity(Day $day)
    {
        $this->bindingEntity = $day;
    }

    /**
     * @return Day
     */
    public function getBindingEntity()
    {
        return $this->bindingEntity;
    }

    public function init()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'story',
            'options' => array(
                'label' => 'Story',
                'show' => true
            )
        ));

        parent::init();

    }

}
