<?php

namespace eCamp\CoreTest\Hydrator;

use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Hydrator\CampTemplateHydrator;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class CampTemplateHydratorTest extends AbstractTestCase {
    public function testExtract(): void {
        $campTemplate = new CampTemplate();
        $campTemplate->setName('name');

        $hydrator = new CampTemplateHydrator();
        $data = $hydrator->extract($campTemplate);

        $this->assertEquals('name', $data['name']);

        // Wie muss das korrekt verglichen werden?
        // ($data['organization'] ist ein LinkEntity
        // $this->assertEquals($organization, $data['organization']);
    }

    public function testHydrate(): void {
        $campTemplate = new CampTemplate();
        $data = [
            'name' => 'name',
        ];

        $hydrator = new CampTemplateHydrator();
        $hydrator->hydrate($data, $campTemplate);

        $this->assertEquals('name', $campTemplate->getName());
    }
}
