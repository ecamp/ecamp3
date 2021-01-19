<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\Lib\Hydrator\Util;
use Laminas\Hydrator\HydratorInterface;

class ContentTypeConfigHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'contentType' => Util::Entity(function (ContentTypeConfig $e) {
                return $e->getContentType();
            }),
        ];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
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
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
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
