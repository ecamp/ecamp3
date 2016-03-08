<?php
namespace EcampDB\Fixtures\Prod;

use EcampCore\Entity\Group;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class Groups extends AbstractFixture implements OrderedFixtureInterface
{
    const PBS = 'group-pbs';
    const LU = 'group-lu';
    const ZG = 'group-zg';

    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'name' => 'PBS',
                'desc' => 'Pfadi Bewegung Schweiz',
                'parent' => null,
                'reference' => self::PBS
            ),
            array(
                'name' => 'Pfadi Luzern',
                'desc' => 'Kantonalverband Pfadi Luzern',
                'parent' => self::PBS,
                'reference' => self::LU
            ),
            array(
                'name' => 'Pfaid Zug',
                'desc' => 'Kantonalverband Pfadi Zug',
                'parent' => self::PBS,
                'reference' => self::ZG
            ),
        ));
    }

    private function load_(ObjectManager $manager, array $config)
    {
        $groupRepo = $manager->getRepository('EcampCore\Entity\Group');

        foreach($config as $groupConfig) {
            $name = $groupConfig['name'];
            $desc = $groupConfig['desc'];
            $parent = $groupConfig['parent'];
            $reference = $groupConfig['reference'];

            if($parent != null){
                $parent = $this->getReference($parent);
            }

            /** @var Group $group */
            $group = $groupRepo->findOneBy(array(
                'parent' => $parent,
                'name' => $name
            ));

            if($group == null){
                $group = new Group($parent);
                $group->setName($name);
                $manager->persist($group);
            }

            $group->setDescription($desc);

            $this->addReference($reference, $group);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 70;
    }
}
