<?php
namespace EcampCore\Filter;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class CampFilter extends InputFilter
{
	public function __construct()
	{
		$this->add(array(
		    'name' => 'name',
		    'required' => true,
		    'validators' => array(
		        array(
		            'name' => 'not_empty',
		        ),
		        array(
		            'name' => 'string_length',
		            'options' => array(
		                'min' => 5
		            ),
		        ),
		    ),
		));
		
		$this->add(array(
			'name' => 'title',
			'required' => true,
			'validators' => array(
					array(
							'name' => 'not_empty',
					),
					array(
							'name' => 'string_length',
							'options' => array(
									'min' => 5
							),
					),
			),
		));
		
	}
}