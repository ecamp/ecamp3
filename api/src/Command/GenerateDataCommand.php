<?php

namespace App\Command;

use App\Service\DataGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: self::APP_GENERATE_DATA_COMMAND,
    description: 'Generate realistic test data for development'
)]
class GenerateDataCommand extends Command {
    public const string APP_GENERATE_DATA_COMMAND = 'app:generate-data';
    private EntityManagerInterface $entityManager;
    private DataGeneratorService $dataGeneratorService;

    public function __construct(EntityManagerInterface $entityManager, DataGeneratorService $dataGeneratorService) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->dataGeneratorService = $dataGeneratorService;
    }

    protected function configure(): void {
        $this
            ->setDescription('Generate realistic test data for development')
            ->addArgument('num-camps', InputArgument::OPTIONAL, 'Number of camps to generate', 250)
            ->addOption('seed', 's', InputOption::VALUE_OPTIONAL, 'Random seed for reproducible data', 12345)
            ->addOption('activities-per-camp', 'a', InputOption::VALUE_OPTIONAL, 'Number of activities per camp', 20)
            ->addOption('schedule-entries-per-activity', 'se', InputOption::VALUE_OPTIONAL, 'Number of schedule entries per activity', 1)
            ->addOption('batch-size', 'b', InputOption::VALUE_OPTIONAL, 'Number of camps to process before flushing', 3)
            ->addOption('add-user-to-camp', 'autc', InputOption::VALUE_OPTIONAL, 'user to add to generated camps', 'sit@example.com')
            ->addOption('replace', null, InputOption::VALUE_OPTIONAL, 'Replace the generated camps.', 'false')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $numCamps = (int) $input->getArgument('num-camps');
        $seed = (int) $input->getOption('seed');
        $activitiesPerCamp = (int) $input->getOption('activities-per-camp');
        $scheduleEntriesPerActivity = (int) $input->getOption('schedule-entries-per-activity');
        $batchSize = (int) $input->getOption('batch-size');
        $addUserToCamp = $input->getOption('add-user-to-camp');
        $replace = $input->getOption('replace');

        if ($numCamps <= 0) {
            $io->error('Number of camps must be greater than 0.');

            return Command::FAILURE;
        }

        if ($numCamps > 10000) {
            $io->warning('Generating more than 10,000 camps may take a very long time.');
            if (!$io->confirm('Do you want to continue?', false)) {
                return Command::FAILURE;
            }
        }

        if ($activitiesPerCamp <= 0 || $scheduleEntriesPerActivity <= 0 || $batchSize <= 0) {
            $io->error('All numeric parameters must be greater than 0.');

            return Command::FAILURE;
        }

        $io->title('Generating Test Data');
        $io->writeln('Configuration:');
        $io->writeln("  - Number of camps: {$numCamps}");
        $io->writeln("  - Random seed: {$seed}");
        $io->writeln("  - Activities per camp: {$activitiesPerCamp}");
        $io->writeln("  - Schedule entries per activity: {$scheduleEntriesPerActivity}");
        $io->writeln("  - Batch size: {$batchSize}");
        $io->writeln("  - Add user to camp: {$addUserToCamp}");
        $io->writeln("  - Replace existing camps: {$replace}");
        $io->newLine();

        $io->progressStart($numCamps + 1);

        if ('true' === $replace) {
            $this->entityManager->createNativeQuery('
            WITH camps as (SELECT id FROM camp WHERE randomlygenerated = :randomlyGenerated),
                checklists as (SELECT id from checklist WHERE campid IN (SELECT id FROM camps)),
                checklist_items as (SELECT id from checklist_item WHERE checklistid IN (SELECT id FROM checklists)),
                deletedChecklistnodeChecklistitem as (DELETE FROM checklistnode_checklistitem WHERE checklistitem_id IN (SELECT id FROM checklist_item)),
                deletedChecklistItems as (DELETE FROM checklist_item WHERE checklistid IN (SELECT id from checklist WHERE campid IN (SELECT id FROM camps))),
                deletedPeriods as (DELETE FROM period WHERE campid IN (SELECT id FROM camps))
            DELETE FROM camp WHERE id IN (SELECT id FROM camps);
            ', new ResultSetMapping())
                ->setParameter('randomlyGenerated', true)
                ->execute()
            ;
        }
        $io->progressAdvance();

        $this->dataGeneratorService->initialize($seed, $addUserToCamp);

        try {
            for ($i = 0; $i < $numCamps; ++$i) {
                $this->dataGeneratorService->createRealisticCamp(
                    $activitiesPerCamp,
                    $scheduleEntriesPerActivity
                );

                if (($i + 1) % $batchSize === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();

                    $this->dataGeneratorService->initialize($seed, $addUserToCamp);
                }

                $io->progressAdvance();
            }

            $this->entityManager->flush();

            $io->progressFinish();

            $io->success("Successfully generated {$numCamps} camps with realistic data.");

            $stats = $this->dataGeneratorService->getStatistics();
            $io->table(['Entity Type', 'Count'], [
                ['Camps', $stats['camps']],
                ['Periods', $stats['periods']],
                ['Days', $stats['days']],
                ['Categories', $stats['categories']],
                ['Activities', $stats['activities']],
                ['Schedule Entries', $stats['scheduleEntries']],
                ['Content Nodes', $stats['contentNodes']],
                ['Material Lists', $stats['materialLists']],
                ['Material Items', $stats['materialItems']],
                ['Camp Collaborations', $stats['campCollaborations']],
                ['Checklists', $stats['checklists']],
                ['Checklist Items', $stats['checklistItems']],
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->progressFinish();
            $io->error("Error generating data: {$e->getMessage()}");
            $io->writeln($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
