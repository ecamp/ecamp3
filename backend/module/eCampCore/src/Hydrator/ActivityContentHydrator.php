<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\ContentType\ContentTypeStrategyInterface;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Entity\EntityLink;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class ActivityContentHydrator implements HydratorInterface {
    /** @var ContentTypeStrategyProvider */
    private $contentTypeStrategyProvider;

    public function __construct(ContentTypeStrategyProvider $contentTypeStrategyProvider) {
        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public static function HydrateInfo() {
        return [
        ];
    }

    /**
     * @param object $object
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return array
     */
    public function extract($object) {
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
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var ActivityContent $activityContent */
        $activityContent = $object;

        if (isset($data['instanceName'])) {
            $activityContent->setInstanceName($data['instanceName']);
        }

        return $activityContent;
    }
}
