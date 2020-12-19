<?php

namespace eCamp\LibTest\Hal;

use eCamp\Lib\Hal\Extractor\LinkExtractor;
use eCamp\Lib\Hal\TemplatedLink;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\Link\LinkUrlBuilder;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\View\Helper\ServerUrl;
use Laminas\View\Helper\Url;

/**
 * @internal
 */
class LinkExtractorTest extends AbstractTestCase {
    private $linkExtractor;

    public function setUp(): void {
        parent::setUp();

        $router = TreeRouteStack::factory([
            'routes' => [
                'lit' => [
                    'type' => 'Literal',
                    'options' => [
                        'route' => '/lit',
                    ],

                    'child_routes' => [
                        'fix' => [
                            'type' => 'Segment',
                            'options' => [
                                'route' => '/fix',
                            ],
                        ],
                        'entity' => [
                            'type' => 'Segment',
                            'options' => [
                                'route' => '/entity[/:id]',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $url = new Url();
        $url->setRouter($router);

        $serverUrl = new ServerUrl();
        $serverUrl->setScheme('https');
        $serverUrl->setHost('ecamp3.ch');
        $serverUrl->setPort(443);

        $linkUrlBuilder = new LinkUrlBuilder($serverUrl, $url);

        $this->linkExtractor = new LinkExtractor($linkUrlBuilder);
        $this->linkExtractor->setUrl($url);
        $this->linkExtractor->setServerUrl($serverUrl);
        $this->linkExtractor->setRouter($router);
    }

    public function testFixSegmentLink() {
        /** @var Link $link */
        $link = Link::factory([
            'rel' => 'self',
            'route' => 'lit/fix',
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertArrayNotHasKey('templated', $res);
        $this->assertEquals('https://ecamp3.ch/lit/fix', $res['href']);
    }

    public function testEntityCollectionLink() {
        /** @var Link $link */
        $link = Link::factory([
            'rel' => 'col',
            'route' => 'lit/entity',
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertArrayNotHasKey('templated', $res);
        $this->assertEquals('https://ecamp3.ch/lit/entity', $res['href']);
    }

    public function testEntityLink() {
        /** @var Link $link */
        $link = Link::factory([
            'rel' => 'col',
            'route' => [
                'name' => 'lit/entity',
                'params' => [
                    'id' => '123',
                ],
            ],
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertArrayNotHasKey('templated', $res);
        $this->assertEquals('https://ecamp3.ch/lit/entity/123', $res['href']);
    }

    public function testTemplatedLink() {
        $link = TemplatedLink::factory([
            'rel' => 'col',
            'route' => 'lit/entity',
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertEquals(true, $res['templated']);
        $this->assertEquals('https://ecamp3.ch/lit/entity{/id}', $res['href']);
    }
}
