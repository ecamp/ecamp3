<?php

namespace App\Tests\Util;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\DayResponsible;
use App\Entity\User;
use App\Util\CamelPascalNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CamelPascalNamingStrategyTest extends TestCase {
    /**
     * @dataProvider getTableNameExamples
     *
     * @param string $className
     * @param string $output
     */
    public function testClassToTableName($className, $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->classToTableName($className);

        // then
        $this->assertEquals($output, $result);
    }

    public function getTableNameExamples() {
        return [
            ['', ''],
            [User::class, 'user'],
            [CampCollaboration::class, 'camp_collaboration'],
            ['CampCollaboration', 'camp_collaboration'],
        ];
    }

    /**
     * @dataProvider getPropertyExamples
     *
     * @param string $input
     * @param string $output
     */
    public function testPropertyToColumnName($input, $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->propertyToColumnName($input);

        // then
        $this->assertEquals($output, $result);
    }

    public function getPropertyExamples() {
        return [
            ['', ''],
            ['camp', 'camp'],
            ['campCollaboration', 'campCollaboration'],
        ];
    }

    /**
     * @dataProvider getEmbeddedFieldExamples
     *
     * @param string $propertyName
     * @param string $embeddedColumnName
     * @param string $output
     */
    public function testEmbeddedFieldToColumnName($propertyName, $embeddedColumnName, $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->embeddedFieldToColumnName($propertyName, $embeddedColumnName);

        // then
        $this->assertEquals($output, $result);
    }

    public function getEmbeddedFieldExamples() {
        return [
            ['', '', ''],
            ['address', 'street', 'addressStreet'],
        ];
    }

    /**
     * @dataProvider getJoinColumnExamples
     *
     * @param string $propertyName
     * @param string $output
     */
    public function testJoinColumnName($propertyName, $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->joinColumnName($propertyName);

        // then
        $this->assertEquals($output, $result);
    }

    public function getJoinColumnExamples() {
        return [
            ['', 'Id'],
            ['camp', 'campId'],
            ['campCollaboration', 'campCollaborationId'],
        ];
    }

    /**
     * @dataProvider getJoinKeyColumnExamples
     *
     * @param string      $entityName
     * @param null|string $referencedColumnName
     * @param string      $output
     */
    public function testJoinKeyColumnName($entityName, $referencedColumnName, $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->joinKeyColumnName($entityName, $referencedColumnName);

        // then
        $this->assertEquals($output, $result);
    }

    public function getJoinKeyColumnExamples() {
        return [
            ['', null, 'Id'],
            ['', 'email', 'Email'],
            [Camp::class, null, 'campId'],
            [CampCollaboration::class, null, 'campCollaborationId'],
            [User::class, 'name', 'userName'],
            [DayResponsible::class, 'name', 'dayResponsibleName'],
        ];
    }
}
