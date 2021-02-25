<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\ContentType\ContentTypeStrategyInterface;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Entity\EntityLink;
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

        $data = [
            'id' => $contentNode->getId(),
            'instanceName' => $contentNode->getInstanceName(),
            'position' => $contentNode->getPosition(),
            'contentTypeName' => $contentType->getName(),

            'parent' => ($contentNode->isRoot() ? null : new EntityLink($contentNode->getParent())),
            'contentType' => new EntityLink($contentNode->getContentType()),

            'activity' => Link::factory([
                'rel' => 'activity',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity',
                    'params' => ['activityId' => $contentNode->getActivity()->getId()],
                ],
            ]),
        ];

        /** @var ContentTypeStrategyInterface $strategy */
        $strategy = $this->contentTypeStrategyProvider->get($contentType);

        if (null != $strategy) {
            $strategyData = $strategy->contentNodeExtract($contentNode);
            $data = array_merge($data, $strategyData);
        }

        return $data;
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
        if (isset($data['position'])) {
            $contentNode->setPosition($data['position']);
        }

        return $contentNode;
    }
}
