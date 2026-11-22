<?php

namespace App\Command;

use App\Service\Image\ImageCleanupService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:cleanup-image-files', description: 'Delete files and camp images unused for more than one week')]
class CleanupImageFilesCommand extends Command {
    public function __construct(private readonly ImageCleanupService $cleanup) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $counts = $this->cleanup->cleanupImageFiles();
        new SymfonyStyle($input, $output)->success(sprintf('Deleted %d camp images and %d files.', ...array_values($counts)));

        return self::SUCCESS;
    }
}
