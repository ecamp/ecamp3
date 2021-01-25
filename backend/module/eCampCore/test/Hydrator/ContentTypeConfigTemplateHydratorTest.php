<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\ContentTypeConfigTemplate;
use eCamp\Core\Hydrator\ContentTypeConfigTemplateHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ContentTypeConfigTemplateHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $contentTypeConfigTemplate = new ContentTypeConfigTemplate();
        $contentTypeConfigTemplate->setMultiple(true);
        $contentTypeConfigTemplate->setRequired(true);

        $hydrator = new ContentTypeConfigTemplateHydrator();
        $data = $hydrator->extract($contentTypeConfigTemplate);

        $this->assertTrue($data['multiple']);
        $this->assertTrue($data['required']);
    }

    public function testHydrate() {
        $contentTypeConfigTemplate = new ContentTypeConfigTemplate();
        $data = [
            'multiple' => true,
            'required' => true,
        ];

        $hydrator = new ContentTypeConfigTemplateHydrator();
        $hydrator->hydrate($data, $contentTypeConfigTemplate);

        $this->assertTrue($contentTypeConfigTemplate->getMultiple());
        $this->assertTrue($contentTypeConfigTemplate->getRequired());
    }
}
