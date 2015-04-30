<?php

namespace EcampLib\Form;

use Zend\Form\Form;

class BaseForm extends Form
{

    public function setAction($url)
    {
        $this->setAttribute('action', $url);
    }

    public function getAction()
    {
        return $this->getAttribute('action');
    }

    /** @deprecated */
    public function copyElements($sourceForm, $elements)
    {
        foreach ($elements as $element) {
            $this->add($sourceForm->get($element));
            //$this->getInputFilter()->add($sourceForm->getInputFilter()->get($element));
        }
    }

}
