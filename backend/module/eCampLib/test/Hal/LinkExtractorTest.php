<?php


namespace eCamp\LibTest\Hal;

use eCamp\Lib\Hal\Extractor\LinkExtractor;
use eCamp\Lib\Hal\TemplatedLink;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Zend\Router\Http\TreeRouteStack;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use ZF\Hal\Link\Link;
use ZF\Hal\Link\LinkUrlBuilder;

class LinkExtractorTest extends AbstractTestCase {
    private $linkExtractor;

    function setUp() {
        parent::setUp();

        $router = TreeRouteStack::factory([
            'routes' => array(
                'lit' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/lit',
                    ),

                    'child_routes' => [
                        'fix' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route' => '/fix',
                            ),
                        ),
                        'entity' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route' => '/entity[/:id]',
                            ),
                        ),
                    ]
                ),
            )
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

    function testFixSegmentLink() {
        /** @var Link $link */
        $link = Link::factory([
            'rel' => 'self',
            'route' => 'lit/fix'
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertArrayNotHasKey('templated', $res);
        $this->assertEquals('https://ecamp3.ch/lit/fix', $res['href']);
    }

    function testEntityCollectionLink() {
        /** @var Link $link */
        $link = Link::factory([
            'rel' => 'col',
            'route' => 'lit/entity'
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertArrayNotHasKey('templated', $res);
        $this->assertEquals('https://ecamp3.ch/lit/entity', $res['href']);
    }

    function testEntityLink() {
        /** @var Link $link */
        $link = Link::factory([
            'rel' => 'col',
            'route' => [
                'name' => 'lit/entity',
                'params' => [
                    'id' => '123'
                ]
            ]
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertArrayNotHasKey('templated', $res);
        $this->assertEquals('https://ecamp3.ch/lit/entity/123', $res['href']);
    }

    function testTemplatedLink() {
        $link = TemplatedLink::factory([
            'rel' => 'col',
            'route' => 'lit/entity',
        ]);

        $res = $this->linkExtractor->extract($link);
        $this->assertEquals(true, $res['templated']);
        $this->assertEquals('https://ecamp3.ch/lit/entity{/id}', $res['href']);
    }
}
