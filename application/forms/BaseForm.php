<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

class Application_Form_BaseForm extends Ztal_Form
{
	public function standardDecorators()
	{
	    $this->clearDecorators();

		$this->addDecorator('FormErrors')
	    ->addDecorator('FormElements')
         ->addDecorator('HtmlTag')
         ->addDecorator('Form');

	    $this->setElementDecorators(array(
            array('ViewHelper'),
            array('Description'),
            array('Label', array('separator'=>' ')),
            array('HtmlTag', array('class'=>'element-group')),
        ));
	}
	
	public function setData($entity)
	{
		$this->getElement('id')->setValue($entity->getId());
		
		foreach( $this->attributes as $attribute )
		{
			$this->getElement($attribute)->setValue($entity->{'get'.ucfirst($attribute)}());
		}
	}
	
	public function setDataFromArray($entity)
	{
		$this->getElement('id')->setValue($entity->getId());
		
		foreach( $this->attributes as $attribute )
		{
			$this->getElement($attribute)->setValue($entity->{'get'.ucfirst($attribute)}());
		}
	}

}