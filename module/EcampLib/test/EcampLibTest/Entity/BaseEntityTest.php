<?php

namespace EcampLibTest\Entity;

use EcampLib\Entity\BaseEntity;

class BaseEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testModifyTime()
    {
        $entity = new DummyEntity();

        $this->assertEquals(0, $entity->getCreatedAt()->getTimestamp());
        $this->assertEquals(0, $entity->getUpdatedAt()->getTimestamp());

        $entity->PrePersist();

        $this->assertGreaterThan(0, $entity->getCreatedAt()->getTimestamp());
        $this->assertGreaterThan(0, $entity->getUpdatedAt()->getTimestamp());
        $this->assertEquals($entity->getCreatedAt(), $entity->getUpdatedAt());

        sleep(1);

        $entity->PreUpdate();
        $this->assertNotEquals($entity->getCreatedAt(), $entity->getUpdatedAt());
    }

    public function testToString()
    {
        $entity = new DummyEntity();

        $this->assertStringStartsWith('[' . DummyEntity::class, $entity->__toString());
        $this->assertStringEndsWith(']', $entity->__toString());
    }
}

class DummyEntity extends BaseEntity
{

}