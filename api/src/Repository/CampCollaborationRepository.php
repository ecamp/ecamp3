<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|CampCollaboration find($id, $lockMode = null, $lockVersion = null)
 * @method null|CampCollaboration findOneBy(array $criteria, array $orderBy = null)
 * @method CampCollaboration[]    findAll()
 * @method CampCollaboration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampCollaborationRepository extends ServiceEntityRepository implements CanFilterByUserInterface {
    use FiltersByCampCollaboration;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, CampCollaboration::class);
    }

    public function findByInviteKeyHash(string $inviteKeyHash): ?CampCollaboration {
        return $this->findOneBy(['inviteKeyHash' => $inviteKeyHash]);
    }

    public function findByUserAndCamp(User $user, Camp $camp): ?CampCollaboration {
        return $this->findOneBy(['user' => $user, 'camp' => $camp]);
    }

    public function findByUserAndIdAndInvited(User $user, string $id): ?CampCollaboration {
        return $this->findOneBy(['user' => $user, 'id' => $id, 'status' => CampCollaboration::STATUS_INVITED]);
    }

    /**
     * @return CampCollaboration[]
     */
    public function findAllByPersonallyInvitedUser(User $user): array {
        return $this->findBy(['user' => $user, 'status' => CampCollaboration::STATUS_INVITED]);
    }

    public function filterByUser(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, User $user): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $this->filterByCampCollaboration($queryBuilder, $user, "{$rootAlias}.camp");
    }
}
