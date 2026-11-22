<?php

namespace App\Service\Image;

use Doctrine\DBAL\Connection;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Clock\ClockInterface;

final readonly class ImageCleanupService {
    public function __construct(
        private Connection $connection,
        private HtmlImageReferences $references,
        private FilesystemOperator $storage,
        private ClockInterface $clock,
    ) {}

    /** @return array{marked: int, reactivated: int} */
    public function cleanupCampImages(): array {
        $counts = ['marked' => 0, 'reactivated' => 0];
        foreach ($this->connection->fetchFirstColumn('SELECT DISTINCT campid FROM camp_image') as $campId) {
            $lastId = '';
            do {
                $rows = $this->connection->transactional(function () use ($campId, $lastId, &$counts): array {
                    $rows = $this->connection->fetchAllAssociative(
                        'SELECT id, sha256, unreferencedsince FROM camp_image WHERE campid = :camp AND id > :last ORDER BY id LIMIT 100',
                        ['camp' => $campId, 'last' => $lastId],
                    );
                    if ([] === $rows) {
                        return $rows;
                    }
                    $referenced = [];
                    foreach ($this->connection->iterateColumn(
                        "SELECT n.data FROM content_node n JOIN view_camp_root_content_nodes owner ON owner.rootcontentnodeid = n.rootid WHERE owner.campid = :camp AND n.strategy IN ('singletext', 'storyboard') AND n.data IS NOT NULL",
                        ['camp' => $campId],
                    ) as $data) {
                        foreach (HtmlImageReferences::htmlFromData(json_decode($data, true, flags: JSON_THROW_ON_ERROR)) as $html) {
                            foreach ($this->references->extract($html) as $hash) {
                                $referenced[$hash] = true;
                            }
                        }
                    }
                    $now = $this->clock->now()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
                    foreach ($rows as $row) {
                        $isReferenced = isset($referenced[$row['sha256']]);
                        if ($isReferenced && null !== $row['unreferencedsince']) {
                            $this->connection->update('camp_image', ['unreferencedsince' => null], ['id' => $row['id']]);
                            ++$counts['reactivated'];
                        } elseif (!$isReferenced && null === $row['unreferencedsince']) {
                            $this->connection->update('camp_image', ['unreferencedsince' => $now], ['id' => $row['id']]);
                            ++$counts['marked'];
                        }
                    }

                    return $rows;
                });
                if ([] !== $rows) {
                    $lastId = $rows[array_key_last($rows)]['id'];
                }
            } while (100 === count($rows));
        }

        return $counts;
    }

    /** @return array{images: int, files: int} */
    public function cleanupImageFiles(): array {
        $counts = ['images' => 0, 'files' => 0];
        $cutoff = $this->clock->now()->setTimezone(new \DateTimeZone('UTC'))->modify('-1 week')->format('Y-m-d H:i:s');
        $lastId = '';
        do {
            $ids = $this->connection->fetchFirstColumn(
                'SELECT id FROM camp_image WHERE id > :last AND unreferencedsince < :cutoff ORDER BY id LIMIT 100',
                ['last' => $lastId, 'cutoff' => $cutoff],
            );
            foreach ($ids as $id) {
                $deleted = $this->connection->transactional(function () use ($id, $cutoff): array {
                    $hash = $this->connection->fetchOne(
                        'DELETE FROM camp_image WHERE id = :id AND unreferencedsince < :cutoff RETURNING sha256',
                        ['id' => $id, 'cutoff' => $cutoff],
                    );
                    if (false === $hash) {
                        return ['images' => 0, 'files' => 0];
                    }
                    $deleteFile = false === $this->connection->fetchOne('SELECT 1 FROM camp_image WHERE sha256 = :hash LIMIT 1', ['hash' => $hash]);
                    if ($deleteFile) {
                        $this->storage->delete('images/'.$hash);
                    }

                    return ['images' => 1, 'files' => (int) $deleteFile];
                });
                $counts['images'] += $deleted['images'];
                $counts['files'] += $deleted['files'];
                $lastId = $id;
            }
        } while (100 === count($ids));

        return $counts;
    }
}
