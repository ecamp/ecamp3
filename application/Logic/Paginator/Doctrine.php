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
 
namespace Logic\Paginator;
 
class Doctrine implements \Zend_Paginator_Adapter_Interface
{

    protected $_query;

    public function __construct(\Doctrine\ORM\QueryBuilder $doctrineQuery)
    {
        $this->_query = $doctrineQuery;
    }

    /**
     *
     * @param integer $offset Record offset
     * @param integer $limit Number of items per page
     * @return array
     * @see Zend_Paginator_Adapter_Interface::getItems()
     */
    public function getItems ($offset, $limit)
    {
		$query = clone $this->_query;
		
        $query
		 ->setFirstResult( $offset )
		 ->setMaxResults( $limit );
		
        return $query->getQuery()->getResult();
    }
	
    /**
     * @see Countable::count()
     */
    public function count ()
    {
        $query = clone $this->_query;
		
		$select = $query->getQuery()->getDQL();
		$select = stristr($select, "FROM", true);
		$select = trim(str_ireplace("SELECT","",$select));
		
        $query->select("count($select) as total");
		
        $rs = $query->getQuery()->getSingleScalarResult();
        return $rs; 
    }
}