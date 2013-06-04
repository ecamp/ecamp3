<?php

namespace Core\Validator\Entity;

class LoginValidator extends \Core\Validator\Entity
{

    protected function init()
    {
        $this->get('password')
            ->addValidator(new \Zend_Validate_StringLength(array('min' => 6)));
    }

}
