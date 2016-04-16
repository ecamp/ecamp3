<?php
namespace EcampDB\Fixtures\Prod;

use EcampCore\Entity\Group;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class GroupFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const PBS = 'group-pbs';
    const AG = 'group-ag';
    const BE = 'group-be';
    const GE = 'group-ge';
    const JU = 'group-ju';
    const LU = 'group-lu';
    const NE = 'group-ne';
    const SO = 'group-so';
    const TG = 'group-tg';
    const VS = 'group-vs';
    const ZG = 'group-zg';
    const ZH = 'group-zh';

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
                'name' => 'Pfadi Argau',
                'desc' => 'Kantonalverband Pfadi Argau',
                'parent' => self::PBS,
                'reference' => self::AG
            ),
            array(
                'name' => 'Pfadi Bern',
                'desc' => 'Kantonalverband Pfadi Bern',
                'parent' => self::PBS,
                'reference' => self::BE
            ),
            array(
                'name' => 'Scouts Genevois',
                'desc' => 'Association du Scoutisme Genevois',
                'parent' => self::PBS,
                'reference' => self::GE
            ),
            array(
                'name' => 'Scouts Jurassien',
                'desc' => 'Association du Scoutisme Jurassien',
                'parent' => self::PBS,
                'reference' => self::JU
            ),
            array(
                'name' => 'Pfadi Luzern',
                'desc' => 'Kantonalverband Pfadi Luzern',
                'parent' => self::PBS,
                'reference' => self::LU
            ),
            array(
                'name' => 'Scouts Neuchâtelois',
                'desc' => 'Association du Scoutisme Neuchâtelois',
                'parent' => self::PBS,
                'reference' => self::NE
            ),
            array(
                'name' => 'Pfadi Solothurn',
                'desc' => 'Kantonalverband Pfadi Solothurn',
                'parent' => self::PBS,
                'reference' => self::SO
            ),
            array(
                'name' => 'Pfadi Thurgau',
                'desc' => 'Kantonalverband Pfadi Thurgau',
                'parent' => self::PBS,
                'reference' => self::TG
            ),
            array(
                'name' => 'Scouts Valaisan',
                'desc' => 'Association du Scoutisme Valaisan',
                'parent' => self::PBS,
                'reference' => self::VS
            ),
            array(
                'name' => 'Pfaid Zug',
                'desc' => 'Kantonalverband Pfadi Zug',
                'parent' => self::PBS,
                'reference' => self::ZG
            ),
            array(
                'name' => 'Pfaid Züri',
                'desc' => 'Kantonalverband Pfadi Züri',
                'parent' => self::PBS,
                'reference' => self::ZH
            ),
        ));
    }

    private function load_(ObjectManager $manager, array $config)
    {
        $groupRepo = $manager->getRepository('EcampCore\Entity\Group');

        foreach ($config as $groupConfig) {
            $name = $groupConfig['name'];
            $desc = $groupConfig['desc'];
            $parent = $groupConfig['parent'];
            $reference = $groupConfig['reference'];

            if ($parent != null) {
                $parent = $this->getReference($parent);
            }

            /** @var Group $group */
            $group = $groupRepo->findOneBy(array(
                'parent' => $parent,
                'name' => $name
            ));

            if ($group == null) {
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
