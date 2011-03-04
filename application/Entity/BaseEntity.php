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

	/**
	 * Returns a form of the model
	 * @param string $type
	 * @return Zend_Form
	 */
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

	/**
	 * Save data to the entity trough the validation/filter chain
	 * @param array $data
	 * @param null|string $form
	 * @param bool $partial
	 * @return bool
	 */
	public function save(array $data, $form = null, $partial = false)
    {
        $form = $this->getForm($form);

	    /* save user entries in the form */
		$form->setAttribs($data);

	    if( $partial )
	    {
		    if (!$form->isValidPartial($data)) {
	            return false;
            }
	    }
	    else {
            if (!$form->isValid($data)) {
	            return false;
            }
        }
		
		$this->updateAttributes($form->getValues());
		
		/* we could persist and flush ourselves here. needs to be discussed */

        return true;
    }

	/**
	 * Save data to the entity through the validation/filter chain (partialValidation)
	 */
	public function savePartial(array $data, $form = null)
    {
        return $this->save($data, $form, true);
    }

	/**
	 * update attributes of an entity by array
	 */
	protected function updateAttributes($data)
	{
		foreach( $data as $key=>$value )
			$this->{"set".ucfirst($key)}($value);
	}

	/**
	 * Magic getter/setter -attention, set*() bypasses the validator. Use save() in general cases. 
	 * @throws \Exception
	 * @param  $function
	 * @param  $args
	 */
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