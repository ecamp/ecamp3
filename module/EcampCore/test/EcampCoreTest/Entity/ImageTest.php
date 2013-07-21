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

        $this->assertEquals(8, $image->getSize());
    }

    public function testImageByUrl()
    {
        $image = new Image(__DIR__ . '/../../../assets/img/avatar.user.png');

        $this->assertEquals('image/png; charset=binary', $image->getMime());
    }

}
