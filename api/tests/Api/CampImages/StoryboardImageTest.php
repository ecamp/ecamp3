<?php

namespace App\Tests\Api\CampImages;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\TestWith;

/** @internal */
class StoryboardImageTest extends ECampApiTestCase {
    private const SECTION = 'cb26d76a-9e3b-43f0-a7c4-0f2ad0ea8029';

    #[TestWith(['POST'])]
    #[TestWith(['PATCH'])]
    public function testPreservesAndReactivatesCampImages(string $method): void {
        $client = static::createClientWithCredentials();
        $image = $this->createImage('camp1');
        $src = '/api/images/'.$image->sha256.'/content';
        $response = $client->request($method, $this->endpoint($method), $this->options($method, $src));

        self::assertResponseStatusCodeSame('POST' === $method ? 201 : 200);
        $html = $response->toArray()['data']['sections'][self::SECTION]['column2Html'];
        self::assertStringContainsString('src="'.$src.'"', $html);
        self::assertStringContainsString('alt="Map"', $html);
        self::assertStringContainsString('width="320"', $html);
        self::assertNull($this->getEntityManager()->getConnection()->fetchOne('SELECT unreferencedsince FROM camp_image WHERE id = :id', ['id' => $image->getId()]));
    }

    #[TestWith(['POST', '/api/images/0000000000000000000000000000000000000000000000000000000000000000/content'])]
    #[TestWith(['PATCH', '/api/images/0000000000000000000000000000000000000000000000000000000000000000/content'])]
    #[TestWith(['POST', 'https://example.org/image.png'])]
    #[TestWith(['PATCH', 'https://example.org/image.png'])]
    public function testRejectsUnknownAndExternalImages(string $method, string $src): void {
        static::createClientWithCredentials()->request($method, $this->endpoint($method), $this->options($method, $src));

        self::assertResponseStatusCodeSame(422);
    }

    #[TestWith(['POST'])]
    #[TestWith(['PATCH'])]
    public function testRejectsImagesOwnedByAnotherCamp(string $method): void {
        $client = static::createClientWithCredentials();
        $image = $this->createImage('camp2');
        $client->request($method, $this->endpoint($method), $this->options($method, '/api/images/'.$image->sha256.'/content'));

        self::assertResponseStatusCodeSame(422);
    }

    private function createImage(string $camp): CampImage {
        $em = $this->getEntityManager();
        $image = new CampImage();
        $image->camp = $em->find(Camp::class, static::getFixture($camp)->getId());
        $image->sha256 = hash('sha256', 'storyboard image');
        $image->mediaType = 'image/avif';
        $image->unreferencedSince = new \DateTime('2020-01-01');
        $em->persist($image);
        $em->flush();

        return $image;
    }

    private function endpoint(string $method): string {
        return '/content_node/storyboards'.('POST' === $method ? '' : '/'.static::getFixture('storyboard1')->getId());
    }

    private function options(string $method, string $src): array {
        $data = ['sections' => [
            'ab9740f6-61a4-4cae-b574-a73aeb7c5ea0' => ['column1' => '10:00', 'column2Html' => '<p>Start</p>', 'column3' => 'Alex', 'position' => 0],
            self::SECTION => ['column1' => '10:15', 'column2Html' => '<p><img src="'.$src.'" alt="Map" width="320"></p>', 'column3' => 'Sam', 'position' => 1],
        ]];
        if ('PATCH' === $method) {
            return ['headers' => ['Content-Type' => 'application/merge-patch+json'], 'json' => ['data' => $data]];
        }

        return ['json' => [
            'parent' => $this->getIriFor('columnLayout1'),
            'contentType' => $this->getIriFor('contentTypeStoryboard'),
            'slot' => '1',
            'position' => 10,
            'data' => $data,
        ]];
    }
}
