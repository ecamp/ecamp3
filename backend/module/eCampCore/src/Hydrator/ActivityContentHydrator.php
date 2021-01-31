<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\ContentType\ContentTypeStrategyInterface;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Entity\EntityLink;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ActivityContentHydrator implements HydratorInterface {
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
        /** @var ActivityContent $activityContent */
        $activityContent = $object;
        $contentType = $activityContent->getContentType();

        $data = [
            'id' => $activityContent->getId(),
            'instanceName' => $activityContent->getInstanceName(),
            'contentTypeName' => $contentType->getName(),

            'contentType' => new EntityLink($activityContent->getContentType()),

            'activity' => Link::factory([
                'rel' => 'activity',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity',
                    'params' => ['activityId' => $activityContent->getActivity()->getId()],
                ],
            ]),
        ];

        /** @var ContentTypeStrategyInterface $strategy */
        $strategy = $this->contentTypeStrategyProvider->get($contentType);

        if (null != $strategy) {
            $strategyData = $strategy->activityContentExtract($activityContent);
            $data = array_merge($data, $strategyData);
        }

        return $data;
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): ActivityContent {
        /** @var ActivityContent $activityContent */
        $activityContent = $object;

        if (isset($data['instanceName'])) {
            $activityContent->setInstanceName($data['instanceName']);
        }

        return $activityContent;
    }
}
