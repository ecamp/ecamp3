<?php

namespace App\Command;

use App\Service\Image\ImageCleanupService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:cleanup-image-blobs', description: 'Delete expired image blobs and their stored files')]
class CleanupImageBlobsCommand extends Command {
    public function __construct(private readonly ImageCleanupService $cleanup) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $counts = $this->cleanup->cleanupImageBlobs();
        new SymfonyStyle($input, $output)->success(sprintf('Image blobs: %d marked, %d reactivated, %d deleted.', ...array_values($counts)));

        return self::SUCCESS;
    }
}
