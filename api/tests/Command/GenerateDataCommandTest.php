<?php

namespace App\Tests\Command;

use App\Command\GenerateDataCommand;
use App\Entity\Profile;
use App\Tests\Api\ECampApiTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
class GenerateDataCommandTest extends ECampApiTestCase {
    private CommandTester $commandTester;

    public function setUp(): void {
        parent::setUp();

        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $command = $application->find(GenerateDataCommand::APP_GENERATE_DATA_COMMAND);
        $this->commandTester = new CommandTester($command);
    }

    public function testGenerateData() {
        /** @var Profile $profile7Manager */
        $profile7Manager = static::getFixture('profile7manager');
        $this->commandTester->execute([
            'num-camps' => '10',
            '--add-user-to-camp' => $profile7Manager->email,
        ]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testReplaceGeneratedData() {
        /** @var Profile $profile7Manager */
        $profile7Manager = static::getFixture('profile7manager');
        $this->commandTester->execute([
            'num-camps' => '10',
            '--add-user-to-camp' => $profile7Manager->email,
            '--replace' => 'true',
        ]);

        $this->commandTester->assertCommandIsSuccessful();

        $this->commandTester->execute([
            'num-camps' => '10',
            '--add-user-to-camp' => $profile7Manager->email,
            '--replace' => 'true',
        ]);

        $this->commandTester->assertCommandIsSuccessful();
    }
}
