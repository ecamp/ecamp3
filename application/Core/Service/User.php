<?php

namespace Core\Service;

class User extends ServiceAbstract
{
	
    // public function index(){}
	public function get(){}
	public function create(){}
	public function update(){}
	public function delete(){}
    
	/**
	 * 
	 * Return the set of roles for the current user based on the context (Group, Camp)
	 * @param unknown_type $group
	 * @param unknown_type $camp
	 */
	public function getCurrentUserRole($group = null, $camp = null){
		$roles = array();
		$roles[] = new \Zend_Acl_Role('member');
		$roles[] = new \Zend_Acl_Role('group_manager');
		$roles[] = new \Zend_Acl_Role('camp_normal');
		
		return $roles;
	}
	
}