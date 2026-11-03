<?php

namespace App\Service\Image;

use App\Entity\ImageBlob;
use Doctrine\DBAL\Connection;
use Symfony\Component\Clock\ClockInterface;

final readonly class ImageCleanupService {
    public function __construct(
        private Connection $connection,
        private ImageLifecycleLock $lock,
        private HtmlImageReferences $references,
        private ImageStorage $storage,
        private ClockInterface $clock,
        private int $campImageRetentionDays,
        private int $imageBlobRetentionDays,
    ) {
        if ($campImageRetentionDays < 1 || $imageBlobRetentionDays < 1) {
            throw new \InvalidArgumentException('Image retention periods must be positive numbers of days.');
        }
    }

    /** @return array{marked: int, reactivated: int, deleted: int} */
    public function cleanupCampImages(): array {
        $counts = ['marked' => 0, 'reactivated' => 0, 'deleted' => 0];
        foreach ($this->connection->fetchFirstColumn('SELECT DISTINCT campid FROM camp_image') as $campId) {
            $lastId = '';
            do {
                $rows = $this->connection->transactional(function () use ($campId, $lastId, &$counts): array {
                    $this->lock->acquire();
                    $rows = $this->connection->fetchAllAssociative(
                        'SELECT id, unreferencedsince FROM camp_image WHERE campid = :camp AND id > :last ORDER BY id LIMIT 100',
                        ['camp' => $campId, 'last' => $lastId],
                    );
                    if ([] === $rows) {
                        return $rows;
                    }
                    $referenced = [];
                    foreach ($this->connection->iterateColumn(
                        "SELECT n.data->>'html' FROM content_node n JOIN view_camp_root_content_nodes owner ON owner.rootcontentnodeid = n.rootid WHERE owner.campid = :camp AND n.strategy = 'singletext'",
                        ['camp' => $campId],
                    ) as $html) {
                        foreach ($this->references->extract($html ?? '') as $imageId => $imageCampId) {
                            if ($imageCampId === $campId) {
                                $referenced[$imageId] = true;
                            }
                        }
                    }
                    foreach ($rows as $row) {
                        $this->updateReference('camp_image', $row, isset($referenced[$row['id']]), $this->campImageRetentionDays, $counts);
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

    /** @return array{marked: int, reactivated: int, deleted: int} */
    public function cleanupImageBlobs(): array {
        $counts = ['marked' => 0, 'reactivated' => 0, 'deleted' => 0];
        $lastId = '';
        do {
            $ids = $this->connection->fetchFirstColumn('SELECT id FROM image_blob WHERE id > :last ORDER BY id LIMIT 100', ['last' => $lastId]);
            foreach ($ids as $id) {
                $this->connection->transactional(function () use ($id, &$counts): void {
                    $this->lock->acquire();
                    $row = $this->connection->fetchAssociative('SELECT id, sha256, unreferencedsince FROM image_blob WHERE id = :id', ['id' => $id]);
                    if (false === $row) {
                        return;
                    }
                    $referenced = false !== $this->connection->fetchOne('SELECT id FROM camp_image WHERE imageblobid = :id LIMIT 1', ['id' => $id]);
                    $this->updateReference('image_blob', $row, $referenced, $this->imageBlobRetentionDays, $counts);
                });
                $lastId = $id;
            }
        } while (100 === count($ids));

        return $counts;
    }

    /**
     * @param array<string, mixed>                               $row
     * @param array{marked: int, reactivated: int, deleted: int} $counts
     */
    private function updateReference(string $table, array $row, bool $referenced, int $days, array &$counts): void {
        $now = $this->clock->now()->setTimezone(new \DateTimeZone('UTC'));
        if ($referenced) {
            if (null !== $row['unreferencedsince']) {
                $this->connection->update($table, ['unreferencedsince' => null], ['id' => $row['id']]);
                ++$counts['reactivated'];
            }
        } elseif (null === $row['unreferencedsince']) {
            $this->connection->update($table, ['unreferencedsince' => $now->format('Y-m-d H:i:s')], ['id' => $row['id']]);
            ++$counts['marked'];
        } elseif (new \DateTimeImmutable($row['unreferencedsince'], new \DateTimeZone('UTC')) < $now->modify('-'.$days.' days')) {
            if ('image_blob' === $table) {
                $blob = new ImageBlob();
                $blob->sha256 = $row['sha256'];
                $this->storage->delete($blob);
            }
            $this->connection->delete($table, ['id' => $row['id']]);
            ++$counts['deleted'];
        }
    }
}
