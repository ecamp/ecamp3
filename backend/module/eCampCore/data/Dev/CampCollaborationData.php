<?php

namespace eCamp\CoreData\Dev;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;

class CampCollaborationData extends AbstractFixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(CampCollaboration::class);
        $campRepository = $manager->getRepository(Camp::class);
        $userRepository = $manager->getRepository(User::class);

        $camps = $campRepository->findAll();
        $users = $userRepository->findAll();

        foreach ($camps as $camp) {
            foreach ($users as $user) {
                /** @var CampCollaboration $collaboration */
                $collaboration = $repository->findOneBy(['camp' => $camp, 'user' => $user]);
                if (null == $collaboration) {
                    $collaboration = new CampCollaboration();
                    $collaboration->setCamp($camp);
                    $collaboration->setUser($user);
                    $collaboration->setRole(CampCollaboration::ROLE_MEMBER);
                    $collaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
                    $collaboration->setCollaborationAcceptedBy('install');
                    $manager->persist($collaboration);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies() {
        return [CampData::class, UserData::class];
    }
}
