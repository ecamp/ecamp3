<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isNull;

/**
 * @internal
 */
class UserTest extends TestCase {
    public function testSerialize() {
        $user = new User();
        $user->state = User::ACTIVATE;
        $user->plainPassword = 'plainpassword';

        $serializedUser = serialize($user);
        $deserializeUser = unserialize($serializedUser);

        assertThat($deserializeUser->plainPassword, isNull());
        assertThat($deserializeUser->state, equalTo(User::ACTIVATE));
    }
}
