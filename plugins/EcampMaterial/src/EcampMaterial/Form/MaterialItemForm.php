<?php

namespace EcampMaterial\Form;

use EcampWeb\Form\ClassMethodsHydrator;

use EcampWeb\Form\AjaxBaseForm;

class MaterialItemForm extends AjaxBaseForm
{
    public function __construct($formElementManager)
    {
        parent::__construct('material-item');
        $this->setHydrator(new ClassMethodsHydrator());

        $sourceForm = $formElementManager->get('EcampMaterial\Entity\MaterialItem');
        $this->copyElements($sourceForm, array('quantity', 'text'));

        $this->setInputFilter($sourceForm->getInputFilter());
        $this->setValidationGroup('quantity','text');
        $this->setOption('layout', 'inline');
        $this->get('quantity')
            ->setOption('column-size','md-2');

        $this->get('text')
            ->setOption('column-size','md-5')
            ->setAttribute('class', 'typeahead');
    }

    public function init()
    {

       // $this->add(new \Zend\Form\Element\Csrf('security'));
    }

}
