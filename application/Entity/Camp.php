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

namespace Entity;
	
/**
 * @Entity
 * @Table(name="Camp")
 * @HasLifecycleCallbacks
 */
class Camp extends BaseEntity
{
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 * @Column(type="string", length=32, nullable=false )
	 */
	private $name;

	/**
	 * @var string
	 * @Column(type="string", length=32, nullable=false )
	 */
	private $slogan;

	/**
	 * @var User
	 * @OneToOne(targetEntity="Entity\User")
	 * @JoinColumn(name="creator_id", referencedColumnName="id")
	 */
	private $creator;

	/**
	 * Page Object
	 * @var ArrayObject
	 *
	 * @OneToMany(targetEntity="Entity\UserToCamp", mappedBy="camp")
	 */
	private $userCamp;

	
	protected $_forms = array();
	
	public function getForm($type = 'camp')
    {
        $type  = ucfirst($type);
        if (!isset($this->_forms[$type])) {
            $class = 'Application_Form_' . $type;
            $this->_forms[$type] = new $class;
        }
		
		$this->_forms[$type]->setData($this);
		
        return $this->_forms[$type];
    }
	
	public function save(array $data)
    {
        $form = $this->getForm();
		
		/* ensure that only data is written that is allowed to */
		foreach( $form->attributes as $attribute )
			$data_access[$attribute] = isset($data[$attribute]) ? $data[$attribute] : $this->{$attribute};
		
        if (!$form->isValid($data_access)) {
            return false;
        }
		
		$this->updateAttributes($data_access);
		
		/* we could persist and flush ourselves here. needs to be discussed */
        return true;
    }
	
	private function updateAttributes($data)
	{
		foreach( $data as $key=>$value )
			$this->{"set".ucfirst($key)}($value);
	}
	
	/* magic getter */
	/* attention, set*() bypasses the validator. Use save() in general cases. */
	public function __call($function , $args) 
	{
		if(strpos($function,'get')===0){
            return $this->{lcfirst(substr($function,3))};
        }
		
		/* magic setter = bad idea, will be removed very soon */
		if(strpos($function,'set')===0){
            return $this->{lcfirst(substr($function,3))}=$args[0];
        }
		
        throw new \Exception('Undefined method \'' .$function . '\' called on ' . get_class($this), 6000);
	}
	
	
}
