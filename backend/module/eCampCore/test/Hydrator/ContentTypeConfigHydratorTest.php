<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\ContentTypeConfig;
use eCamp\Core\Hydrator\ContentTypeConfigHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ContentTypeConfigHydratorTest extends AbstractTestCase {
    public function testExtract() {
        $contentTypeConfig = new ContentTypeConfig();
        $contentTypeConfig->setMultiple(true);
        $contentTypeConfig->setRequired(true);

        $hydrator = new ContentTypeConfigHydrator();
        $data = $hydrator->extract($contentTypeConfig);

        $this->assertTrue($data['multiple']);
        $this->assertTrue($data['required']);
    }

    public function testHydrate() {
        $contentTypeConfig = new ContentTypeConfig();
        $data = [
            'multiple' => true,
            'required' => true,
        ];

        $hydrator = new ContentTypeConfigHydrator();
        $hydrator->hydrate($data, $contentTypeConfig);

        $this->assertTrue($contentTypeConfig->getMultiple());
        $this->assertTrue($contentTypeConfig->getRequired());
    }
}
