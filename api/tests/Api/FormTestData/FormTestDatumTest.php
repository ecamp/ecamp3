<?php

namespace App\Tests\Api\FormTestData;

use App\Entity\FormTestDatum;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class FormTestDatumTest extends ECampApiTestCase {
    public function testGetCollectionIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', '/form_test_data');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetItemIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('GET', $this->itemIri());
        $this->assertResponseStatusCodeSame(401);
    }

    public function testPatchIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('PATCH', $this->itemIri(), [
            'json' => ['text' => 'nope'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetCollectionContainsExactlyTheSingleRow() {
        static::createClientWithCredentials()->request('GET', '/form_test_data');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['totalItems' => 1]);
    }

    public function testGetItemReturnsTheRow() {
        static::createClientWithCredentials()->request('GET', $this->itemIri());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'text' => 'Hello',
            'multilineText' => "Line one\nLine two",
            'html' => '<p>Rich <strong>text</strong></p>',
            'number' => 42,
            'flag' => true,
            'color' => '#1976d2',
            'date' => '2024-01-15',
            'time' => '2024-01-15T09:30:00+00:00',
            'language' => 'en',
            'languageMultiselect' => ['en', 'de'],
        ]);
    }

    public function testPatchUpdatesFields() {
        static::createClientWithCredentials()->request('PATCH', $this->itemIri(), [
            'json' => ['text' => 'updated', 'number' => 7, 'flag' => false],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'text' => 'updated',
            'number' => 7,
            'flag' => false,
        ]);
    }

    public function testPatchUpdatesLanguageFields() {
        static::createClientWithCredentials()->request('PATCH', $this->itemIri(), [
            'json' => ['language' => 'fr', 'languageMultiselect' => ['fr', 'it']],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'language' => 'fr',
            'languageMultiselect' => ['fr', 'it'],
        ]);
    }

    public function testPatchUpdatesTimeField() {
        static::createClientWithCredentials()->request('PATCH', $this->itemIri(), [
            'json' => ['time' => '2024-02-16T18:45:00+00:00'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['time' => '2024-02-16T18:45:00+00:00']);
    }

    public function testCreateIsNotAllowed() {
        static::createClientWithCredentials()->request('POST', '/form_test_data', [
            'json' => ['text' => 'new'],
        ]);
        $this->assertResponseStatusCodeSame(405);
    }

    public function testDeleteIsNotAllowed() {
        static::createClientWithCredentials()->request('DELETE', $this->itemIri());
        $this->assertResponseStatusCodeSame(405);
    }

    private function itemIri(): string {
        /** @var FormTestDatum $entity */
        $entity = static::getFixture('formTestDatum');

        return '/form_test_data/'.$entity->getId();
    }
}
