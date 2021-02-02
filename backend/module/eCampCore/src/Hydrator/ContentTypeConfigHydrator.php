<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\Lib\Hydrator\Util;
use Laminas\Hydrator\HydratorInterface;

class ContentTypeConfigHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
            'contentType' => Util::Entity(function (ContentTypeConfig $e) {
                return $e->getContentType();
            }),
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var ContentTypeConfig $contentTypeConfig */
        $contentTypeConfig = $object;

        return [
            'id' => $contentTypeConfig->getId(),
            'required' => $contentTypeConfig->getRequired(),
            'multiple' => $contentTypeConfig->getMultiple(),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): ContentTypeConfig {
        /** @var ContentTypeConfig $contentTypeConfig */
        $contentTypeConfig = $object;

        if (isset($data['required'])) {
            $contentTypeConfig->setRequired($data['required']);
        }
        if (isset($data['multiple'])) {
            $contentTypeConfig->setMultiple($data['multiple']);
        }

        return $contentTypeConfig;
    }
}
