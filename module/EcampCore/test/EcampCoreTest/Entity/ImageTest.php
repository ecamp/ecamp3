<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{

    public function testData()
    {
        $image = new Image();

        $image->setData('imagesrc');
        $image->setMime('png');

        $this->assertEquals('imagesrc', $image->getData());
        $this->assertEquals('png', $image->getMime());
    }

}
