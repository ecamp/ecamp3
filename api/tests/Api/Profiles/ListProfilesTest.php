<?php

namespace App\Tests\Api\Profiles;

use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
class ListProfilesTest extends ECampApiTestCase {
    public function testListProfilesIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/profiles');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testListProfilesIsAllowedForLoggedInUserButFiltered() {
        // precondition: There are multiple profiles that the user doesn't have access to
        $this->assertNotEmpty(static::$fixtures['profile4unrelated']);
        $this->assertNotEmpty(static::$fixtures['profile5inactive']);
        $this->assertNotEmpty(static::$fixtures['profile6invited']);
        $this->assertNotEmpty(static::$fixtures['profileWithoutCampCollaborations']);
        $this->assertNotEmpty(static::$fixtures['profileWithStateDeleted']);

        $response = static::createClientWithCredentials()->request('GET', '/profiles');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 5,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile1manager')],
            ['href' => $this->getIriFor('profile2member')],
            ['href' => $this->getIriFor('profile3guest')],
            ['href' => $this->getIriFor('profile7manager')],
            ['href' => $this->getIriFor('profile8memberOnlyInCamp2')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesForLoggedInUserWithoutCampCollaborationShowsOnlyThemself() {
        $profile = static::getFixture('profileWithoutCampCollaborations');
        $response = static::createClientWithCredentials(['email' => $profile->email])
            ->request('GET', '/profiles')
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 1,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profileWithoutCampCollaborations')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesFilteredByCampIsAllowedForCollaborator() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalItems' => 4,
            '_links' => [
                'items' => [],
            ],
            '_embedded' => [
                'items' => [],
            ],
        ]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile1manager')],
            ['href' => $this->getIriFor('profile2member')],
            ['href' => $this->getIriFor('profile3guest')],
            ['href' => $this->getIriFor('profile7manager')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesFilteredByCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testListProfilesFilteredByCampForInactiveCollaboratorShowsOnlyThemself() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId())
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile5inactive')],
        ], $response->toArray()['_links']['items']);
    }

    public function testListProfilesFilteredByCampPrototypeIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('campPrototype');
        $response = static::createClientWithCredentials()->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    #[DataProvider('provideSearchTermsMatchingProfile1Manager')]
    public function testSearchProfilesMatchesFirstnameSurnameNicknameAndEmail(string $searchTerm) {
        $response = static::createClientWithCredentials()
            ->request('GET', '/profiles?search='.urlencode($searchTerm))
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertContains(
            ['href' => $this->getIriFor('profile1manager')],
            $response->toArray()['_links']['items'],
            "Searching for '{$searchTerm}' should return profile1manager."
        );
    }

    public static function provideSearchTermsMatchingProfile1Manager(): array {
        return [
            'full firstname' => ['Robert'],
            'partial firstname' => ['ober'],
            'firstname different case' => ['robert'],
            'full surname' => ['Baden-Powell'],
            'partial surname' => ['aden-Pow'],
            'surname uppercase' => ['BADEN-POWELL'],
            'full nickname' => ['Bi-Pi'],
            'partial nickname' => ['i-P'],
            'full email' => ['test@example.com'],
            'partial email' => ['test@exa'],
        ];
    }

    public function testSearchProfilesIsRestrictedToTheMatchingProfiles() {
        $response = static::createClientWithCredentials()
            ->request('GET', '/profiles?search=Baden-Powell')
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile1manager')],
        ], $response->toArray()['_links']['items']);
    }

    public function testSearchProfilesStaysScopedToRelatedProfilesForUnrelatedUser() {
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('GET', '/profiles?search=Baden-Powell')
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testSearchProfilesWithoutMatchReturnsEmptyResult() {
        $response = static::createClientWithCredentials()
            ->request('GET', '/profiles?search=thisdoesnotmatchanyprofile')
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 0]);
        $this->assertArrayNotHasKey('items', $response->toArray()['_links']);
    }

    public function testSearchProfilesIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/profiles?search=Robert');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testSearchProfilesCanBeCombinedWithCampFilter() {
        $camp = static::getFixture('camp1');
        $response = static::createClientWithCredentials()
            ->request('GET', '/profiles?user.collaborations.camp=%2Fcamps%2F'.$camp->getId().'&search=Baden-Powell')
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
        $this->assertEqualsCanonicalizing([
            ['href' => $this->getIriFor('profile1manager')],
        ], $response->toArray()['_links']['items']);
    }

    #[DataProvider('provideInvalidUtf8SearchTerms')]
    public function testSearchProfilesWithInvalidUtf8ReturnsBadRequest(string $invalidSearch) {
        static::createClientWithCredentials()
            ->request('GET', '/profiles?search='.$invalidSearch)
        ;
        $this->assertResponseStatusCodeSame(400);
    }

    public static function provideInvalidUtf8SearchTerms(): array {
        return [
            'lone UTF-8 continuation byte' => ['%C2'],
            'truncated multibyte sequence' => ['Rob%C3'],
            'invalid byte 0xFF' => ['%FF'],
        ];
    }

    #[DataProvider('provideWeirdSearchTerms')]
    public function testSearchProfilesNeverCausesServerError(string $weirdSearch) {
        $response = static::createClientWithCredentials()
            ->request('GET', '/profiles?search='.$weirdSearch)
        ;
        $this->assertContains(
            $response->getStatusCode(),
            [200, 400],
            "Search term '{$weirdSearch}' must not cause a server error."
        );
    }

    public static function provideWeirdSearchTerms(): array {
        return [
            'lone UTF-8 continuation byte' => ['%C2'],
            'truncated multibyte sequence' => ['Rob%C3'],
            'invalid byte 0xFF' => ['%FF'],
            'null byte' => ['Rob%00ert'],
            'only LIKE wildcards' => ['%25%25%25'],
            'backslash' => ['%5C'],
            'valid umlaut' => ['M%C3%BCller'],
            'emoji' => ['%F0%9F%98%80'],
        ];
    }
}
