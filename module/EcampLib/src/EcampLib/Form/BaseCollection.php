<?php

namespace EcampLib\Form;

use Zend\Form\Element\Collection;

class BaseCollection extends Collection
{
    private $_targetElement = null;

    public function __construct($name = null, $options = array())
    {
        if (isset($options['target_element'])) {
            $this->_targetElement = $options['target_element'];
            unset($options['target_element']);
        }

        parent::__construct($name, $options);
    }

    public function init()
    {
        parent::init();

        if ($this->_targetElement) {
            $this->setOptions(array('target_element' => $this->_targetElement));
        }
    }

    public function getLastChildIndex()
    {
        return $this->lastChildIndex;
    }

}
