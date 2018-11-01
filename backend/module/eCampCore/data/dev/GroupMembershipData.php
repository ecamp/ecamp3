<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\User;

class GroupMembershipData extends AbstractFixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(GroupMembership::class);
        $groupRepository = $manager->getRepository(Group::class);
        $userRepository = $manager->getRepository(User::class);

        $groups = $groupRepository->findAll();
        $users = $userRepository->findAll();

        foreach ($groups as $group) {
            foreach ($users as $user) {
                /** @var GroupMembership $membership */
                $membership = $repository->findOneBy(['group' => $group, 'user' => $user]);
                if ($membership == null) {
                    $membership = new GroupMembership();
                    $membership->setGroup($group);
                    $membership->setUser($user);
                    $membership->setRole(GroupMembership::ROLE_MEMBER);
                    $membership->setStatus(GroupMembership::STATUS_ESTABLISHED);
                    $membership->setMembershipAcceptedBy('install');
                    $manager->persist($membership);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies() {
        return [ GroupData::class, UserData::class ];
    }
}
