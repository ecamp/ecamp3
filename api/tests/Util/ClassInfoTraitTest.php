<?php

namespace App\Tests\Util;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Util\ClassInfoTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\ProxyFactory;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

/**
 * @internal
 */
class ClassInfoTraitTest extends ApiTestCase {
    use ClassInfoTrait;

    private ProxyFactory $proxyFactory;

    public function setUp(): void {
        parent::setUp();

        /** @var EntityManagerInterface $obj */
        $obj = self::getContainer()->get(EntityManagerInterface::class);
        $this->proxyFactory = $obj->getProxyFactory();
    }

    public function testGetClassOfObject() {
        assertThat($this->getObjectClass(new SomeClass()), equalTo(SomeClass::class));
    }

    public function testGetClassEntity() {
        assertThat($this->getObjectClass(new User()), equalTo(User::class));
    }

    public function testGetClassOfProxy() {
        $userProxy = $this->proxyFactory->getProxy(User::class, ['id' => 'test']);
        assertThat($this->getObjectClass($userProxy), equalTo(User::class));
    }
}

class SomeClass {
}
