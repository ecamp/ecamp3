<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\ContentTypeConfigTemplate;
use eCamp\Lib\Hydrator\Util;
use Laminas\Hydrator\HydratorInterface;

class ContentTypeConfigTemplateHydrator implements HydratorInterface {
    public static function HydrateInfo() {
        return [
            'contentType' => Util::Entity(function (ContentTypeConfigTemplate $e) {
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
        /** @var ContentTypeConfigTemplate $contentTypeConfigTemplate */
        $contentTypeConfigTemplate = $object;

        return [
            'id' => $contentTypeConfigTemplate->getId(),
            'required' => $contentTypeConfigTemplate->getRequired(),
            'multiple' => $contentTypeConfigTemplate->getMultiple(),
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var ContentTypeConfigTemplate $contentTypeConfigTemplate */
        $contentTypeConfigTemplate = $object;

        if (isset($data['required'])) {
            $contentTypeConfigTemplate->setRequired($data['required']);
        }
        if (isset($data['multiple'])) {
            $contentTypeConfigTemplate->setMultiple($data['multiple']);
        }

        return $contentTypeConfigTemplate;
    }
}
