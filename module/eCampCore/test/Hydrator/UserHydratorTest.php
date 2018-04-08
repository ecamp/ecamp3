<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\UserHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class UserHydratorTest extends AbstractTestCase
{
    public function testExtract()
    {
        $user = new User();
        $user->setUsername('test');

        $hydrator = new UserHydrator();
        $data = $hydrator->extract($user);

        $this->assertEquals('test', $data['username']);
    }

    public function testHydrate()
    {
        $user = new User();

        $hydrator = new UserHydrator();
        $hydrator->hydrate(['username' => 'test'], $user);

        $this->assertEquals('test', $user->getUsername());
    }
}
