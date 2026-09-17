<?php

namespace App\Tests\Command;

use App\Command\GeneratePasswordListCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @internal
 */
class GeneratePasswordListCommandTest extends TestCase {
    public function testGeneratesThreeThousandNormalizedPasswords(): void {
        $source = "FirstPassword\nfirstpassword\nshort\nümlaut-password\n";
        $expected = ['FirstPassword', 'ümlaut-password'];
        for ($i = 0; $i < 3000; ++$i) {
            $source .= sprintf("unique-password-%04d\r\n", $i);
            if ($i < 2998) {
                $expected[] = sprintf('unique-password-%04d', $i);
            }
        }
        $outputPath = tempnam(sys_get_temp_dir(), 'password-list-test-');
        self::assertNotFalse($outputPath);

        try {
            $command = new GeneratePasswordListCommand(
                new MockHttpClient(new MockResponse($source)),
                $outputPath
            );
            $tester = new CommandTester($command);

            $tester->execute([]);

            $tester->assertCommandIsSuccessful();
            $passwords = file($outputPath, FILE_IGNORE_NEW_LINES);
            self::assertSame($expected, $passwords);
        } finally {
            unlink($outputPath);
        }
    }

    public function testDoesNotReplaceListWhenSourceHasTooFewPasswords(): void {
        $outputPath = tempnam(sys_get_temp_dir(), 'password-list-test-');
        self::assertNotFalse($outputPath);
        file_put_contents($outputPath, "existing-password\n");

        try {
            $command = new GeneratePasswordListCommand(
                new MockHttpClient(new MockResponse("only-password\n")),
                $outputPath
            );
            $tester = new CommandTester($command);

            $tester->execute([]);

            self::assertSame(1, $tester->getStatusCode());
            self::assertSame("existing-password\n", file_get_contents($outputPath));
        } finally {
            unlink($outputPath);
        }
    }

    public function testDoesNotReplaceListWhenSourceResponseIsNotSuccessful(): void {
        $outputPath = tempnam(sys_get_temp_dir(), 'password-list-test-');
        self::assertNotFalse($outputPath);
        file_put_contents($outputPath, "existing-password\n");
        $source = '';
        for ($i = 0; $i < 3000; ++$i) {
            $source .= sprintf("unique-password-%04d\n", $i);
        }

        try {
            $command = new GeneratePasswordListCommand(
                new MockHttpClient(new MockResponse($source, ['http_code' => 503])),
                $outputPath
            );
            $tester = new CommandTester($command);

            $tester->execute([]);

            self::assertSame(1, $tester->getStatusCode());
            self::assertSame("existing-password\n", file_get_contents($outputPath));
        } finally {
            unlink($outputPath);
        }
    }
}
