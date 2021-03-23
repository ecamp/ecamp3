<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\ContentType\ContentTypeStrategyInterface;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContentNodeHydrator implements HydratorInterface {
    private ContentTypeStrategyProvider $contentTypeStrategyProvider;

    public function __construct(ContentTypeStrategyProvider $contentTypeStrategyProvider) {
        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public static function HydrateInfo(): array {
        return [
            'children' => Util::Collection(function (ContentNode $e) {
                return new EntityLinkCollection($e->getChildren());
            }, function (ContentNode $e) {
                return [
                    Link::factory([
                        'rel' => 'children',
                        'route' => [
                            'name' => 'e-camp-api.rest.doctrine.content-node',
                            'options' => ['query' => ['parentId' => $e->getId()]],
                        ],
                    ]),
                ];
            }),
        ];
    }

    /**
     * @param object $object
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function extract($object): array {
        /** @var ContentNode $contentNode */
        $contentNode = $object;
        $contentType = $contentNode->getContentType();
        $owner = $contentNode->getOwner();

        $data = [
            'id' => $contentNode->getId(),
            'instanceName' => $contentNode->getInstanceName(),
            'slot' => $contentNode->getSlot(),
            'position' => $contentNode->getPosition(),
            'jsonConfig' => $contentNode->getJsonConfig(),
            'contentTypeName' => $contentType->getName(),

            'parent' => ($contentNode->isRoot() ? null : new EntityLink($contentNode->getParent())),
            'root' => new EntityLink($contentNode->getRoot()),
            'children' => new EntityLinkCollection($contentNode->getChildren()),
            'childrenLink' => Link::factory([
                'rel' => 'children',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.content-node',
                    'options' => ['query' => ['parentId' => $contentNode->getId()]],
                ],
            ]),
            'contentType' => new EntityLink($contentNode->getContentType()),
        ];

        if ($owner instanceof Activity) {
            $data['owner'] = Link::factory([
                'rel' => 'owner',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity',
                    'params' => ['activityId' => $owner->getId()],
                ],
            ]);
            $data['ownerCategory'] = Link::factory([
                'rel' => 'ownerCategory',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.category',
                    'params' => ['categoryId' => $owner->getCategory()->getId()],
                ],
            ]);
        }
        if ($owner instanceof Category) {
            $data['owner'] = Link::factory([
                'rel' => 'owner',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.category',
                    'params' => ['categoryId' => $owner->getId()],
                ],
            ]);
            $data['ownerCategory'] = $data['owner'];
        }

        /** @var ContentTypeStrategyInterface $strategy */
        $strategy = $this->contentTypeStrategyProvider->get($contentType);
        $strategyData = $strategy->contentNodeExtract($contentNode);

        return array_merge($data, $strategyData);
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = $object;

        if (isset($data['instanceName'])) {
            $contentNode->setInstanceName($data['instanceName']);
        }
        if (isset($data['slot'])) {
            $contentNode->setSlot($data['slot']);
        }
        if (isset($data['position'])) {
            $contentNode->setPosition($data['position']);
        }
        if (isset($data['jsonConfig'])) {
            $jsonConfig = (array) ($data['jsonConfig']);
            $contentNode->setJsonConfig($jsonConfig);
        }

        return $contentNode;
    }
}
