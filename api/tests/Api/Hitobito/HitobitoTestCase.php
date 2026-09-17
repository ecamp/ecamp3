<?php

declare(strict_types=1);

namespace App\Tests\Api\Hitobito;

use App\Entity\Camp;
use App\Repository\CampRepository;
use App\Service\Hitobito\HitobitoProvider;
use App\Service\Hitobito\MockClient;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @internal
 */
abstract class HitobitoTestCase extends ECampApiTestCase {
    /**
     * Links the given fixture camp to Hitobito.
     */
    protected function linkCampToEvent(string $fixtureName, string $eventId = MockClient::EVENT_ID_LEADER): Camp {
        /** @var Camp $camp */
        $camp = static::getFixture($fixtureName);

        /** @var CampRepository $campRepository */
        $campRepository = static::getContainer()->get(CampRepository::class);
        $camp = $campRepository->find($camp->getId());

        $camp->hitobitoProvider = HitobitoProvider::PBSMIDATA;
        $camp->hitobitoEventId = $eventId;

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $entityManager->flush();

        return $camp;
    }
}
