<?php

namespace EcampCore\Form\Element;

use EcampCore\Entity\CampCollaboration;
use Zend\Form\Element\Select;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CollaborationRoleSelectFactory implements FactoryInterface
{
    /**
     * @param  \Zend\Form\FormElementManager $formElementManager
     * @return \Zend\Form\Element\Select
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $select = new Select();
        $select->setEmptyOption('Select ...');
        $select->setOptions(array(
            'options' => array(
                CampCollaboration::ROLE_MANAGER => 'camp manager',
                CampCollaboration::ROLE_MEMBER => 'camp member'
            )
        ));

        return $select;
    }
}
