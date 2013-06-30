<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\User;
use EcampCore\Entity\UserRelationship;

class UserRelationshipTest extends \PHPUnit_Framework_TestCase
{
    public function testUnrelatedUsers()
    {
        $a = new User();
        $b = new User();

        $this->assertCount(0, $a->userRelationship()->getFriends());
        $this->assertCount(0, $b->userRelationship()->getFriends());

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertFalse($b->userRelationship()->isFriend($a));

        $this->assertCount(0, $a->userRelationship()->getSentFriendshipRequests());
        $this->assertCount(0, $b->userRelationship()->getSentFriendshipRequests());

        $this->assertCount(0, $a->userRelationship()->getReceivedFriendshipRequests());
        $this->assertCount(0, $b->userRelationship()->getReceivedFriendshipRequests());

        $this->assertFalse($a->userRelationship()->hasSentFriendshipRequestTo($b));
        $this->assertFalse($a->userRelationship()->hasReceivedFriendshipRequestFrom($b));

        $this->assertFalse($b->userRelationship()->hasSentFriendshipRequestTo($a));
        $this->assertFalse($b->userRelationship()->hasReceivedFriendshipRequestFrom($a));

        $this->assertTrue($a->userRelationship()->canSendFriendshipRequest($b));
        $this->assertTrue($b->userRelationship()->canSendFriendshipRequest($a));
    }

    public function testFriendshpRequests()
    {
        $a = new User();
        $b = new User();

        $ur = new UserRelationship($a, $b);

        $this->assertCount(0, $a->userRelationship()->getFriends());
        $this->assertCount(0, $b->userRelationship()->getFriends());

        $this->assertFalse($a->userRelationship()->isFriend($b));
        $this->assertFalse($b->userRelationship()->isFriend($a));

        $this->assertContains($b, $a->userRelationship()->getSentFriendshipRequests());
        $this->assertCount(0, $a->userRelationship()->getReceivedFriendshipRequests());

        $this->assertCount(0, $b->userRelationship()->getSentFriendshipRequests());
        $this->assertContains($a, $b->userRelationship()->getReceivedFriendshipRequests());

        $this->assertTrue($a->userRelationship()->hasSentFriendshipRequestTo($b));
        $this->assertFalse($a->userRelationship()->hasReceivedFriendshipRequestFrom($b));

        $this->assertFalse($b->userRelationship()->hasSentFriendshipRequestTo($a));
        $this->assertTrue($b->userRelationship()->hasReceivedFriendshipRequestFrom($a));

        $this->assertFalse($a->userRelationship()->canSendFriendshipRequest($b));
        $this->assertFalse($b->userRelationship()->canSendFriendshipRequest($a));
    }

    public function testFriendship()
    {
        $a = new User();
        $b = new User();

        $ur = new UserRelationship($a, $b);
        $cp = new UserRelationship($b, $a);

        UserRelationship::Link($ur, $cp);

        $this->assertContains($b, $a->userRelationship()->getFriends());
        $this->assertContains($a, $b->userRelationship()->getFriends());

        $this->assertTrue($a->userRelationship()->isFriend($b));
        $this->assertTrue($b->userRelationship()->isFriend($a));

        $this->assertCount(0, $a->userRelationship()->getSentFriendshipRequests());
        $this->assertCount(0, $b->userRelationship()->getSentFriendshipRequests());

        $this->assertCount(0, $a->userRelationship()->getReceivedFriendshipRequests());
        $this->assertCount(0, $b->userRelationship()->getReceivedFriendshipRequests());

        $this->assertFalse($a->userRelationship()->hasSentFriendshipRequestTo($b));
        $this->assertFalse($a->userRelationship()->hasReceivedFriendshipRequestFrom($b));

        $this->assertFalse($b->userRelationship()->hasSentFriendshipRequestTo($a));
        $this->assertFalse($b->userRelationship()->hasReceivedFriendshipRequestFrom($a));

        $this->assertFalse($a->userRelationship()->canSendFriendshipRequest($b));
        $this->assertFalse($b->userRelationship()->canSendFriendshipRequest($a));
    }

}
