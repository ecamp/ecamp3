<?php

namespace App\Tests\Api\Comments;

use App\Entity\Comment;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateCommentTest extends ECampApiTestCase {
    public function testPatchCommentIsDeniedForAnonymousUser() {
        $comment = static::getFixture('comment1');
        static::createBasicClient()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'resolved' => true,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchCommentIsDeniedForUnrelatedUser() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
                'resolved' => true,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
        $this->assertNull($this->getEntityManager()->getRepository(Comment::class)->find($comment->getId())->resolvedAt);
    }

    public function testPatchCommentIsDeniedForInactiveCollaborator() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
                'resolved' => true,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testResolveCommentIsAllowedForGuest() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
                'resolved' => true,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'resolvedBy' => ['href' => $this->getIriFor('campCollaboration3guest')],
            ],
        ]);
    }

    public function testResolveCommentIsAllowedForMember() {
        $comment = static::getFixture('comment1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
                'resolved' => true,
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'resolvedBy' => ['href' => $this->getIriFor('campCollaboration2member')],
            ],
        ]);
        $this->assertNotNull($response->toArray()['resolvedAt']);
        $this->assertArrayNotHasKey('resolved', $response->toArray());
    }

    public function testResolveCommentIsAllowedForManager() {
        $comment = static::getFixture('comment1');
        $response = static::createClientWithCredentials()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'resolved' => true,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'resolvedBy' => ['href' => $this->getIriFor('campCollaboration1manager')],
            ],
        ]);
        $this->assertNotNull($response->toArray()['resolvedAt']);
    }

    public function testResolveCommentAgainKeepsTheOriginalResolver() {
        $comment = static::getFixture('comment4resolved');
        $response = static::createClientWithCredentials()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'resolved' => true,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'resolvedBy' => ['href' => $this->getIriFor('campCollaboration2member')],
            ],
        ]);
        $this->assertEquals(
            $comment->resolvedAt->getTimestamp(),
            (new \DateTime($response->toArray()['resolvedAt']))->getTimestamp()
        );
    }

    public function testReopenComment() {
        $comment = static::getFixture('comment4resolved');
        $response = static::createClientWithCredentials()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'resolved' => false,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertNull($response->toArray()['resolvedAt']);
        $this->assertNull($response->toArray()['_links']['resolvedBy']);

        $comment = $this->getEntityManager()->getRepository(Comment::class)->find($comment->getId());
        $this->assertNull($comment->resolvedAt);
        $this->assertNull($comment->resolvedBy);
    }

    public function testResolveReplyIsNotAllowed() {
        $comment = static::getFixture('comment3reply');
        static::createClientWithCredentials()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'resolved' => true,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'resolved',
                    'message' => 'Only the root comment of a thread can be resolved.',
                ],
            ],
        ]);
        $this->assertNull($this->getEntityManager()->getRepository(Comment::class)->find($comment->getId())->resolvedAt);
    }

    public function testPatchCommentTextIsNotAllowed() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'textHtml' => 'hacked',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("textHtml" is unknown).',
        ]);
        $this->assertEquals(
            $comment->textHtml,
            $this->getEntityManager()->getRepository(Comment::class)->find($comment->getId())->textHtml
        );
    }

    public function testPatchCommentParentIsNotAllowed() {
        $comment = static::getFixture('comment2');
        static::createClientWithCredentials()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'parent' => $this->getIriFor('comment1'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("parent" is unknown).',
        ]);
    }

    public function testPatchCommentResolvedAtIsNotAllowed() {
        $comment = static::getFixture('comment1');
        static::createClientWithCredentials()->request('PATCH', '/comments/'.$comment->getId(), ['json' => [
            'resolvedAt' => '2023-05-01T12:00:00+00:00',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("resolvedAt" is unknown).',
        ]);
    }
}
