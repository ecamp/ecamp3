<?php

namespace App\Repository;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|CampCollaboration find($id, $lockMode = null, $lockVersion = null)
 * @method null|CampCollaboration findOneBy(array $criteria, array $orderBy = null)
 * @method CampCollaboration[]    findAll()
 * @method CampCollaboration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampCollaborationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, CampCollaboration::class);
    }

    public function findByInviteKey(string $inviteKey): ?CampCollaboration {
        return $this->findOneBy(['inviteKey' => $inviteKey]);
    }

    public function findByUserAndCamp(User $user, Camp $camp): ?CampCollaboration {
        return $this->findOneBy(['user' => $user, 'camp' => $camp]);
    }
}
