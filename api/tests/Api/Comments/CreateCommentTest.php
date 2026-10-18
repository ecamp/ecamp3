<?php

namespace App\Tests\Api\Comments;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\Comment;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCommentTest extends ECampApiTestCase {
    public function testCreateCommentIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/comments', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateCommentIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/comments', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateCommentIsAllowedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/comments', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateCommentIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/comments', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateCommentSetsTheCampCollaborationAsAuthor() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/comments', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'authorDisplayName' => null,
            '_links' => [
                'author' => ['href' => $this->getIriFor('campCollaboration2member')],
            ],
        ]);
    }

    public function testCreateCommentIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateCommentInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campPrototype'),
            'activity' => $this->getIriFor('activity1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCommentInSharedCampIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campShared'),
            'activity' => $this->getIriFor('activity1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCommentInSharedCampIsDeniedForInactiveUser() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])->request('POST', '/comments', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campShared'),
            'activity' => $this->getIriFor('activity1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCommentInSharedCampIsDeniedForInvitedUser() {
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])->request('POST', '/comments', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campShared'),
            'activity' => $this->getIriFor('activity1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateCommentValidatesMissingText() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload([], ['textHtml'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'textHtml',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateCommentValidatesTextMaxLength() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload([
            'textHtml' => str_repeat('a', 1025),
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'textHtml',
                    'message' => 'This value is too long. It should have 1024 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateCommentRejectsAuthorInPayload() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload(['author' => $this->getIriFor('user1manager')])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("author" is unknown).',
        ]);
    }

    public function testCreateCommentRejectsActivityCampMismatch() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload(['camp' => $this->getIriFor('camp2')])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'activity: Must belong to the same camp.',
        ]);
    }

    public function testCreateCommentFiltersMaliciousHtml() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload(['textHtml' => '<b>testText</b><script>alert(1)</script>'])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(['textHtml' => '<b>testText</b>']));
    }

    public function testCreateReply() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/comments', ['json' => [
                'parent' => $this->getIriFor('comment1'),
                'textHtml' => 'A reply',
            ]])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'textHtml' => 'A reply',
            '_links' => [
                'parent' => ['href' => $this->getIriFor('comment1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'activity' => ['href' => $this->getIriFor('activity1')],
                'author' => ['href' => $this->getIriFor('campCollaboration2member')],
            ],
        ]);
    }

    public function testCreateReplyMayRepeatTheAnchorOfItsParent() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload([
            'parent' => $this->getIriFor('comment1'),
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '_links' => [
                'activity' => ['href' => $this->getIriFor('activity1')],
            ],
        ]);
    }

    public function testCreateReplyToCampLevelComment() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => [
            'parent' => $this->getIriFor('comment4resolved'),
            'textHtml' => 'A reply',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '_links' => [
                'activity' => null,
                'parent' => ['href' => $this->getIriFor('comment4resolved')],
                'camp' => ['href' => $this->getIriFor('camp1')],
            ],
        ]);
    }

    public function testCreateReplyToOrphanedCommentKeepsItsDescription() {
        $parent = $this->getEntityManager()->getRepository(Comment::class)->find(static::getFixture('comment3')->getId());
        $parent->activity = null;
        $parent->orphanDescription = 'LS Sportolympiade';
        $this->getEntityManager()->flush();

        static::createClientWithCredentials()->request('POST', '/comments', ['json' => [
            'parent' => $this->getIriFor('comment3'),
            'textHtml' => 'A reply',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'orphanDescription' => 'LS Sportolympiade',
        ]);
    }

    public function testCreateReplyIsNotPossibleForUnrelatedUser() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/comments', ['json' => [
                'parent' => $this->getIriFor('comment1'),
                'textHtml' => 'A reply',
            ]])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Item not found for "'.$this->getIriFor('comment1').'".',
        ]);
    }

    public function testCreateReplyIsDeniedForInactiveCollaborator() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/comments', ['json' => [
                'parent' => $this->getIriFor('comment1'),
                'textHtml' => 'A reply',
            ]])
        ;

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateReplyToReplyIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => [
            'parent' => $this->getIriFor('comment3reply'),
            'textHtml' => 'A reply',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Cannot reply to a reply.',
                ],
            ],
        ]);
    }

    public function testCreateReplyValidatesActivityOfParent() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => [
            'activity' => $this->getIriFor('activity2'),
            'parent' => $this->getIriFor('comment1'),
            'textHtml' => 'A reply',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must be about the same activity as the comment it replies to.',
                ],
            ],
        ]);
    }

    public function testCreateReplyWithActivityToCampLevelCommentIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => [
            'activity' => $this->getIriFor('activity1'),
            'parent' => $this->getIriFor('comment4resolved'),
            'textHtml' => 'A reply',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must be about the same activity as the comment it replies to.',
                ],
            ],
        ]);
    }

    public function testCreateReplyValidatesCampOfParent() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => [
            'camp' => $this->getIriFor('camp2'),
            'parent' => $this->getIriFor('comment4resolved'),
            'textHtml' => 'A reply',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'parent',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testCreateCommentWithoutCampOrParentIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => [
            'textHtml' => 'A comment about nothing',
        ]]);

        // without a camp, nobody is a collaborator of it
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateCommentRejectsResolvedInPayload() {
        static::createClientWithCredentials()->request('POST', '/comments', ['json' => $this->getExampleWritePayload([
            'resolved' => true,
        ])]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("resolved" is unknown).',
        ]);
    }

    #[\Override]
    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Comment::class,
            Post::class,
            array_merge([
                'camp' => $this->getIriFor('camp1'),
                'activity' => $this->getIriFor('activity1'),
            ], $attributes),
            ['author', 'parent'],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Comment::class,
            Get::class,
            $attributes,
            ['camp', 'activity', 'author', 'parent', 'children', 'resolvedBy', 'authorDisplayName'],
            $except
        );
    }
}
