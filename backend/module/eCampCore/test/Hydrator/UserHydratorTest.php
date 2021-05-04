<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\UserHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class UserHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $user = new User();
        $user->setUsername('test');
        $user->setFirstname('fname');
        $user->setSurname('sname');
        $user->setNickname('nname');

        $hydrator = new UserHydrator();
        $data = $hydrator->extract($user);

        $this->assertEquals('test', $data['username']);
        $this->assertEquals('fname', $data['firstname']);
        $this->assertEquals('sname', $data['surname']);
        $this->assertEquals('nname', $data['nickname']);
    }

    public function testHydrate(): void {
        $user = new User();
        $data = [
            'username' => 'test',
            'firstname' => 'fname',
            'surname' => 'sname',
            'nickname' => 'nname',
        ];

        $hydrator = new UserHydrator();
        $hydrator->hydrate($data, $user);

        $this->assertEquals('test', $user->getUsername());
        $this->assertEquals('fname', $user->getFirstname());
        $this->assertEquals('sname', $user->getSurname());
        $this->assertEquals('nname', $user->getNickname());
    }
}
