<?php

namespace App\Command;

use App\Service\Image\ImageCleanupService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:cleanup-camp-images', description: 'Mark unused camp images and delete expired references')]
class CleanupCampImagesCommand extends Command {
    public function __construct(private readonly ImageCleanupService $cleanup) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $counts = $this->cleanup->cleanupCampImages();
        new SymfonyStyle($input, $output)->success(sprintf('Camp images: %d marked, %d reactivated, %d deleted.', ...array_values($counts)));

        return self::SUCCESS;
    }
}
