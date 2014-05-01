<?php

namespace EcampLib\Form;

use Zend\Form\Form;

class BaseForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }

    public function getIterator()
    {
        return new FilteredIterator($this->iterator->getIterator(), $this->getShowedElementNames());
    }

    public function getShowedElementNames()
    {
        $showedElements = array();

        /* @var $child \Zend\Form\Element */
        foreach ($this->iterator as $child) {
            if (!isset($child->options['show']) || $child->options['show'] == true) {
                $showedElements[] = $child->getName();
            }
        }

        return $showedElements;
    }

}
