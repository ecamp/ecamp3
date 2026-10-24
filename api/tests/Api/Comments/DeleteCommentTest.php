<?php

namespace App\Tests\Api\Comments;

use App\Entity\Comment;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteCommentTest extends ECampApiTestCase {
    public function testDeleteCommentIsDeniedForAnonymousUser() {
        $comment = static::getFixture('comment1');
        static::createBasicClient()->request('DELETE', '/comments/'.$comment->getId());

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteCommentIsDeniedForNonAuthor() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteCommentIsDeniedForManagerInSameCamp() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials(['email' => static::$fixtures['user7manager']->getEmail()])
            ->request('DELETE', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteCommentIsAllowedForAuthor() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials()->request('DELETE', '/comments/'.$comment->getId());

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Comment::class)->find($comment->getId()));
    }

    public function testDeleteCommentIsDeniedForInactiveAuthor() {
        $comment = static::getFixture('comment2');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/comments/'.$comment->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteReplyIsDeniedForManagerInSameCamp() {
        $reply = static::getFixture('comment3reply');
        static::createClientWithCredentials()->request('DELETE', '/comments/'.$reply->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
        $this->assertNotNull($this->getEntityManager()->getRepository(Comment::class)->find($reply->getId()));
    }

    public function testDeleteCommentWhoseAuthorLeftTheCampIsDenied() {
        $comment = $this->getEntityManager()->getRepository(Comment::class)->find(static::getFixture('comment1')->getId());
        $comment->author = null;
        $comment->authorDisplayName = 'Former Manager';
        $this->getEntityManager()->flush();

        static::createClientWithCredentials()->request('DELETE', '/comments/'.$comment->getId());

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteCommentWithOnlyOwnRepliesAlsoDeletesTheReplies() {
        $comment = static::getFixture('comment1');
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $reply = $client->request('POST', '/comments', ['json' => [
            'camp' => $this->getIriFor('camp1'),
            'activity' => $this->getIriFor('activity1'),
            'parent' => $this->getIriFor('comment1'),
            'textHtml' => 'my own reply',
        ]])->toArray();
        $this->assertResponseStatusCodeSame(201);

        $client->request('DELETE', '/comments/'.$comment->getId());

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Comment::class)->find($comment->getId()));
        $this->assertNull($this->getEntityManager()->getRepository(Comment::class)->find($reply['id']));
    }

    public function testDeleteCommentWithRepliesIsDenied() {
        $comment = static::getFixture('comment3');
        static::createClientWithCredentials()->request('DELETE', '/comments/'.$comment->getId());

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'children: It\'s not possible to delete a comment as long as other people have replied to it.',
        ]);
        $this->assertNotNull($this->getEntityManager()->getRepository(Comment::class)->find($comment->getId()));
        $this->assertNotNull($this->getEntityManager()->getRepository(Comment::class)->find(static::getFixture('comment3reply')->getId()));
    }

    public function testDeleteCommentIsAllowedAfterItsRepliesWereDeleted() {
        $comment = static::getFixture('comment3');
        $reply = static::getFixture('comment3reply');

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/comments/'.$reply->getId())
        ;
        $this->assertResponseStatusCodeSame(204);

        static::createClientWithCredentials()->request('DELETE', '/comments/'.$comment->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Comment::class)->find($comment->getId()));
    }
}
