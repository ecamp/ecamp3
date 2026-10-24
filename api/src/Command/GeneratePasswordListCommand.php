<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: self::APP_GENERATE_PASSWORD_LIST_COMMAND,
    description: 'Generate the local compromised-password list'
)]
class GeneratePasswordListCommand extends Command {
    public const string APP_GENERATE_PASSWORD_LIST_COMMAND = 'app:generate-password-list';
    private const string SOURCE_URL = 'https://raw.githubusercontent.com/danielmiessler/SecLists/607c3293b4cb9d40f582c964cee4a4251195d117/Passwords/xato-net-10-million-passwords.txt';
    private const int REQUIRED_COUNT = 3000;
    private const string OUTPUT_PATH = __DIR__.'/../Validator/PwnedPasswords/password-list.txt';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?string $outputPath = null
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $outputPath = $this->outputPath ?? self::OUTPUT_PATH;
        $sourcePath = tempnam(\dirname($outputPath), 'password-list-source-');
        $temporaryOutputPath = tempnam(\dirname($outputPath), 'password-list-output-');

        if (false === $sourcePath || false === $temporaryOutputPath) {
            $io->error('Unable to create temporary files.');

            return self::FAILURE;
        }

        $replaced = false;

        try {
            $this->download($sourcePath);
            $count = $this->writeList($sourcePath, $temporaryOutputPath);
            if (self::REQUIRED_COUNT !== $count) {
                throw new \RuntimeException(sprintf('Only %d qualifying passwords were available; refusing to replace the local list.', $count));
            }

            chmod($temporaryOutputPath, 0644);
            if (!rename($temporaryOutputPath, $outputPath)) {
                throw new \RuntimeException('Unable to replace the local password list.');
            }
            $replaced = true;

            $io->success(sprintf('Generated %d passwords from %s.', $count, self::SOURCE_URL));

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            @unlink($sourcePath);
            if (!$replaced) {
                @unlink($temporaryOutputPath);
            }
        }
    }

    private function download(string $path): void {
        $response = $this->httpClient->request('GET', self::SOURCE_URL);
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \RuntimeException(sprintf('Refusing to process source response with HTTP status %d.', $statusCode));
        }

        $handle = fopen($path, 'wb');
        if (false === $handle) {
            throw new \RuntimeException('Unable to open the source temporary file.');
        }

        try {
            foreach ($this->httpClient->stream($response) as $chunk) {
                $content = $chunk->getContent();
                if ('' !== $content && false === fwrite($handle, $content)) {
                    throw new \RuntimeException('Unable to write the source temporary file.');
                }
            }
        } finally {
            fclose($handle);
        }
    }

    private function writeList(string $sourcePath, string $outputPath): int {
        $input = fopen($sourcePath, 'rb');
        $output = fopen($outputPath, 'wb');
        if (false === $input || false === $output) {
            throw new \RuntimeException('Unable to open temporary password files.');
        }

        $seen = [];
        $count = 0;

        try {
            while (false !== ($line = fgets($input))) {
                $password = rtrim($line, "\r\n");
                $length = mb_strlen($password, 'UTF-8');
                if ($length < 12) {
                    continue;
                }

                $key = mb_strtolower($password, 'UTF-8');
                if (isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;
                if (false === fwrite($output, $password."\n")) {
                    throw new \RuntimeException('Unable to write the output temporary file.');
                }
                ++$count;
                if (self::REQUIRED_COUNT === $count) {
                    break;
                }
            }
        } finally {
            fclose($input);
            fclose($output);
        }

        return $count;
    }
}
