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

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

use EcampLib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="images")
 */
class Image extends BaseEntity
{

    public function __construct($url = null)
    {
        parent::__construct();

        if ($url != null) {
            $this->setByUrl($url);
        }
    }

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $imageMime;

    /**
     * @ORM\Column(type="object", nullable=false)
     */
    private $imageData;

    /**
     * @return string
     */
    public function getData()
    {
        return base64_decode($this->imageData);
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->imageData = base64_encode($data);
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->imageMime;
    }

    /**
     * @param string $mime
     */
    public function setMime($mime)
    {
        $this->imageMime = $mime;
    }

    public function getSize()
    {
        return strlen($this->getData());
    }

    /**
     * @param string $url
     */
    public function setByUrl($url)
    {
        $fi = new \finfo(FILEINFO_MIME);

        $this->setData(file_get_contents($url));
        $this->imageMime = $fi->file($url);

    }

}
