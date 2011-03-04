<?php

namespace Entity;

/**
 * @MappedSuperclass 
 * @HasLifecycleCallbacks
 */
abstract class BaseEntity
{
	/** @Column(name="created_at", type="datetime") */
	private $createdAt;

	/** @Column(name="updated_at", type="datetime") */
	private $updatedAt;

	protected $_forms = array();
	
	/**
	 * @PrePersist
	 */
	public function PrePersist()
	{
		$this->createdAt = new \DateTime("now");
		$this->updatedAt = new \DateTime("now");
	}

	/**
	 * @PreUpdate
	 */
	public function PreUpdate()
	{
		$this->updatedAt = new \DateTime("now");
	}
	
	public function getForm($type = null)
    {
		$type = isset($type) ? $type : $this->defaultForm;
		
        $type  = ucfirst($type);
        if (!isset($this->_forms[$type])) {
            $class = 'Application_Form_' . $type;
            $this->_forms[$type] = new $class;
        }
		
        return $this->_forms[$type];
    }
	
	public function save(array $data)
    {
        $form = $this->getForm();
		
		/* ensure that only data is written that is allowed to */
		//foreach( $form->attributes as $attribute )
		//	$data_access[$attribute] = isset($data[$attribute]) ? $data[$attribute] : $this->{$attribute};
		
        if (!$form->isValid($data)) {
            return false;
        }
		
		$this->updateAttributes($form->getValues());
		
		/* we could persist and flush ourselves here. needs to be discussed */
        return true;
    }
	
	public function savePartial(array $data)
    {
        $form = $this->getForm();
		
		/* ensure that only data is written that is allowed to */
		//foreach( $form->attributes as $attribute )
		//	$data_access[$attribute] = isset($data[$attribute]) ? $data[$attribute] : $this->{$attribute};
		
        if (!$form->isValidPartial($data)) {
            return false;
        }
		
		$this->updateAttributes($form->getValues());
		
		/* we could persist and flush ourselves here. needs to be discussed */
        return true;
    }
	
	protected function updateAttributes($data)
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