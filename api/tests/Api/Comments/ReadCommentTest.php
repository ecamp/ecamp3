<?php

namespace App\Tests\Api\Comments;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadCommentTest extends ECampApiTestCase {
    public function testGetSingleCommentIsDeniedForAnonymousUser() {
        $comment = static::getFixture('comment1');
        static::createBasicClient()->request('GET', '/comments/'.$comment->getId());

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSingleCommentIsNotAllowedForOutsiderAuthor() {
        $comment = static::getFixture('comment2');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCommentIsAllowedForCollaborator() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('GET', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $comment->getId(),
            'textHtml' => $comment->textHtml,
            'createTime' => $comment->getCreateTime()->format(\DateTime::W3C),
        ]);
    }

    public function testGetSingleCommentIsDeniedForUnrelatedUser() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCommentInCampPrototypeIsDeniedForUnrelatedUser() {
        $comment = static::getFixture('comment1campPrototype');
        static::createClientWithCredentials()
            ->request('GET', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCommentInSharedCampIsDeniedForUnrelatedUser() {
        $comment = static::getFixture('comment1campShared');
        static::createClientWithCredentials()
            ->request('GET', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCommentInSharedCampIsDeniedForInactiveUser() {
        $comment = static::getFixture('comment1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSingleCommentInSharedCampIsDeniedForInvitedUser() {
        $comment = static::getFixture('comment1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('GET', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }
}
