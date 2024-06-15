<?php

namespace App\Tests\Util;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\DayResponsible;
use App\Entity\User;
use App\Util\CamelPascalNamingStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CamelPascalNamingStrategyTest extends TestCase {
    #[DataProvider('getTableNameExamples')]
    public function testClassToTableName(string $className, string $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->classToTableName($className);

        // then
        $this->assertEquals($output, $result);
    }

    public static function getTableNameExamples() {
        return [
            ['', ''],
            [User::class, 'user'],
            [CampCollaboration::class, 'camp_collaboration'],
            ['CampCollaboration', 'camp_collaboration'],
        ];
    }

    #[DataProvider('getPropertyExamples')]
    public function testPropertyToColumnName(string $input, string $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->propertyToColumnName($input, '');

        // then
        $this->assertEquals($output, $result);
    }

    public static function getPropertyExamples() {
        return [
            ['', ''],
            ['camp', 'camp'],
            ['campCollaboration', 'campCollaboration'],
        ];
    }

    #[DataProvider('getEmbeddedFieldExamples')]
    public function testEmbeddedFieldToColumnName(string $propertyName, string $embeddedColumnName, string $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->embeddedFieldToColumnName($propertyName, $embeddedColumnName);

        // then
        $this->assertEquals($output, $result);
    }

    public static function getEmbeddedFieldExamples() {
        return [
            ['', '', ''],
            ['address', 'street', 'addressStreet'],
        ];
    }

    #[DataProvider('getJoinColumnExamples')]
    public function testJoinColumnName(string $propertyName, string $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->joinColumnName($propertyName);

        // then
        $this->assertEquals($output, $result);
    }

    public static function getJoinColumnExamples() {
        return [
            ['', 'Id'],
            ['camp', 'campId'],
            ['campCollaboration', 'campCollaborationId'],
        ];
    }

    #[DataProvider('getJoinKeyColumnExamples')]
    public function testJoinKeyColumnName(string $entityName, ?string $referencedColumnName, string $output) {
        // given
        $strategy = new CamelPascalNamingStrategy();

        // when
        $result = $strategy->joinKeyColumnName($entityName, $referencedColumnName);

        // then
        $this->assertEquals($output, $result);
    }

    public static function getJoinKeyColumnExamples() {
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
