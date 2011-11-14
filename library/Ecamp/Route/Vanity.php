<?php
/*
 * Copyright (C) 2011 Urban Suppiger
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
 
namespace Ecamp\Route;

/**
 * Default router which treats :user, :group and :camp specially
 */
class Vanity extends \Zend_Controller_Router_Route
{
	private $em;

	/**
     * Prepares the route for mapping by splitting (exploding) it
     * to a corresponding atomic parts. These parts are assigned
     * a position which is later used for matching and preparing values.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param array $reqs Regular expression requirements for variables (keys as variable names)
     */
    public function __construct($route, $defaults = array(), $reqs = array())
    {	
        $this->_route = trim($route, $this->_urlDelimiter);
        $this->_defaults     = (array)$defaults;
		$this->_requirements = (array) $reqs;
		
		if ($route !== '') {
            foreach (explode($this->_urlDelimiter, $route) as $pos => $part) {
                if (substr($part, 0, 1) == $this->_urlVariable) {
                    $name = substr($part, 1);

                    $this->_parts[$pos]     = (isset($reqs[$name]) ? $reqs[$name] : $this->_defaultRegex);
                    $this->_variables[$pos] = $name;
                } else {       
                    $this->_parts[$pos] = $part;

                    if ($part !== '*') {
                        $this->_staticCount++;
                    }
                }
            }
        }
    }
	
	/**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
	public function match($path, $partial = false)
    {
        $pathStaticCount = 0;
        $values          = array();
        $matchedPath     = '';

        if (!$partial) {
            $path = trim($path, $this->_urlDelimiter);
        }

        if ($path !== '') {
            $path = explode($this->_urlDelimiter, $path);

			$path_pos = 0;
			$route_pos = 0;
			
            while( $path_pos < count($path) ) {
				$pathPart = $path[$path_pos];
				
                // Path is longer than a route, it's not a match
                if (!array_key_exists($route_pos, $this->_parts)) {
                    if ($partial) {
                        break;
                    } else {
                        return false;
                    }
                }

                $matchedPath .= $pathPart . $this->_urlDelimiter;

                // If it's a wildcard, get the rest of URL as wildcard data and stop matching
                if ($this->_parts[$route_pos] == '*') {
                    $count = count($path);
                    for($i = $path_pos; $i < $count; $i+=2) {
                        $var = urldecode($path[$i]);
                        if (!isset($this->_wildcardData[$var]) && !isset($this->_defaults[$var]) && !isset($values[$var])) {
                            $this->_wildcardData[$var] = (isset($path[$i+1])) ? urldecode($path[$i+1]) : null;
                        }
                    }

                    $matchedPath = implode($this->_urlDelimiter, $path);
                    break;
                }

                $name     = isset($this->_variables[$route_pos]) ? $this->_variables[$route_pos] : null;
                $pathPart = urldecode($pathPart);

                // Translate value if required
                $part = $this->_parts[$route_pos];

                // If it's a static part, match directly
                if ($name === null && $part != $pathPart) {
                    return false;
                }
				
				// If it's a user, search username
				if ($name == "user" ) 
				{
					$this->em = \Zend_Registry::get('doctrine')->getEntityManager();
					$user = $this->em->getRepository('Core\Entity\User')->findOneBy(array("username" => $pathPart));

					if (! $user) {
						return false;
					}
					$pathPart= $user->getId();
				// If it's a group, search hierarhical for the groupname
				} elseif ($name == "group") {
					
					$this->em = \Zend_Registry::get('doctrine')->getEntityManager();
					$group = $this->em->getRepository('Core\Entity\Group')->findOneBy(array("name" => $pathPart));
					
					$groupid = null;
					
					while( $group )
					{
						$groupid = $group->getId();
						
						if( !isset($path[$path_pos+1]) ){
							$path_pos++;
							break;
						}
						
						$group = $this->em->getRepository("Core\Entity\Group")->findOneBy(array("name" => urldecode($path[$path_pos+1]), "parent" => $group->getId()));
						
						$path_pos++;
					}
					
					$path_pos--;
					
					if( !$groupid ){
						return false;
					}

					$pathPart= $groupid;
					
				// If it's a camp, search for a camp owned by this user
				} elseif ($name == "camp") {
					
					if( isset($values["user"]) )
					{
						$camp = $this->em->getRepository("Core\Entity\Camp")->findOneBy(array("owner" => $values["user"], "name" => $pathPart));
						
						if (!$camp) {
							return false;
						}

						$pathPart= $camp->getId();
					}
					elseif( isset($values["group"]) )
					{
						$camp = $this->em->getRepository("Core\Entity\Camp")->findOneBy(array("group" => $values["group"], "name" => $pathPart));
						
						if (! $camp) {
							return false;
						}
						
						$pathPart= $camp->getId();
					}
					else
					{
						// TODO: quick-url for camp a la http://ecamp/sola2011
					}
                // If it's a variable with requirement, match a regex. If not - everything matches
                } elseif ($part !== null && !preg_match($this->_regexDelimiter . '^' . $part . '$' . $this->_regexDelimiter . 'iu', $pathPart)) {
                    return false;
                }

                // If it's a variable store it's value for later
                if ($name !== null) {
                    $values[$name] = $pathPart;
                } else {
                    $pathStaticCount++;
                }
				
				$path_pos++;
				$route_pos++;
            }
        }

        // Check if all static mappings have been matched
        if ($this->_staticCount != $pathStaticCount) {
            return false;
        }

        $return = $values + $this->_wildcardData + $this->_defaults;

        // Check if all map variables have been initialized
        foreach ($this->_variables as $var) {
            if (!array_key_exists($var, $return)) {
                return false;
            }
        }

        $this->setMatchedPath(rtrim($matchedPath, $this->_urlDelimiter));

        $this->_values = $values;

        return $return;

    }
	
	/**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param  array $data An array of variable and value pairs used as parameters
     * @param  boolean $reset Whether or not to set route defaults with those provided in $data
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
    {
		$this->em = \Zend_Registry::get('doctrine')->getEntityManager();
	    
        $url  = array();
        $flag = false;

        foreach ($this->_parts as $key => $part) {
            $name = isset($this->_variables[$key]) ? $this->_variables[$key] : null;

            $useDefault = false;
            if (isset($name) && array_key_exists($name, $data) && $data[$name] === null) {
                $useDefault = true;
            }

            if (isset($name)) {
				if($name == "user")
				{
					if(!isset($data[$name]))
					{
						require_once 'Zend/Controller/Router/Exception.php';
						throw new \Zend_Controller_Router_Exception($name . ' is not specified');
					}
					
					$user = $this->em->getRepository('Core\Entity\User')->find($data[$name]);
					
					if (!$user) {
						require_once 'Zend/Controller/Router/Exception.php';
						throw new \Zend_Controller_Router_Exception('user with id '.$data[$name].' does not exist');
					}

					$value = $user->getUsername();
					unset($data[$name]);
					
				} elseif($name == "group") {
				
					if(!isset($data[$name]))
					{
						require_once 'Zend/Controller/Router/Exception.php';
						throw new \Zend_Controller_Router_Exception($name . ' is not specified');
					}
					
					$group = $this->em->getRepository('Core\Entity\Group')->find($data[$name]);
					
					if(!$group) {
						require_once 'Zend/Controller/Router/Exception.php';
						throw new \Zend_Controller_Router_Exception('group with id '.$data[$name].' does not exist');
					}
					
					if ($encode) 
						$grouppath = urlencode($group->getName());
					else
						$grouppath = $group->getName();
						
					while( $group->getParent() != null )
					{
						$group = $group->getParent();
	
						if ($encode) 
							$grouppath = urlencode($group->getName()).'/'.$grouppath;
						else
							$grouppath = $group->getName().'/'.$grouppath;
					}

					$value= $grouppath;
					unset($data[$name]);
					
				} elseif($name == "camp") {
				
					if(!isset($data[$name]))
					{
						require_once 'Zend/Controller/Router/Exception.php';
						throw new \Zend_Controller_Router_Exception($name . ' is not specified');
					}
					
					$camp = $this->em->getRepository('Core\Entity\Camp')->find($data[$name]);
					
					if (!$camp) {
						require_once 'Zend/Controller/Router/Exception.php';
						throw new \Zend_Controller_Router_Exception('camp with id '.$data[$name].' does not exist');
					}

					$value= $camp->getName();
					unset($data[$name]);
					
                } elseif (isset($data[$name]) && !$useDefault) {
                    $value = $data[$name];
                    unset($data[$name]);
                } elseif (!$reset && !$useDefault && isset($this->_values[$name])) {
                    $value = $this->_values[$name];
                } elseif (!$reset && !$useDefault && isset($this->_wildcardData[$name])) {
                    $value = $this->_wildcardData[$name];
                } elseif (isset($this->_defaults[$name])) {
                    $value = $this->_defaults[$name];
                } else {
                    require_once 'Zend/Controller/Router/Exception.php';
                    throw new \Zend_Controller_Router_Exception($name . ' is not specified');
                }

                
                $url[$key] = $value;
            } elseif ($part != '*') {
				$url[$key] = $part;
            } else {
                if (!$reset) $data += $this->_wildcardData;
                $defaults = $this->getDefaults();
                foreach ($data as $var => $value) {
                    if ($value !== null && (!isset($defaults[$var]) || $value != $defaults[$var])) {
                        $url[$key++] = $var;
                        $url[$key++] = $value;
                        $flag = true;
                    }
                }
            }
        }

        $return = '';

        foreach (array_reverse($url, true) as $key => $value) {
            $defaultValue = null;

            if (isset($this->_variables[$key])) {
                $defaultValue = $this->getDefault($this->_variables[$key]);
            }

            if ($flag || $value !== $defaultValue || $partial) {
				if(!isset($this->_variables[$key]) || ! $this->_variables[$key] == ":group")
					if ($encode) $value = urlencode($value);
                $return = $this->_urlDelimiter . $value . $return;
                $flag = true;
            }
        }

        return trim($return, $this->_urlDelimiter);

    }
}
