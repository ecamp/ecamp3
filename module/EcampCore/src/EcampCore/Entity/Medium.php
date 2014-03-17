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

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\MediumRepository", readOnly=true)
 * @ORM\Table(name="media")
 */
class Medium
{
    const MEDIUM_WEB = 'web';
    const MEDIUM_PRINT = 'print';
    const MEDIUM_MOBILE = 'mobile';

    public function __construct()
    {
    }

    /**
     * Short human readable name
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $name;

    public function getName()
    {
        return $this->name;
    }

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $default;

    public function isDefault()
    {
        return $this->default;
    }
}
