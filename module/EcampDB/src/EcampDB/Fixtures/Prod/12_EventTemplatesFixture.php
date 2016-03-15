<?php

namespace EcampDB\Fixtures\Prod;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\EventTemplate;
use EcampCore\Entity\EventTemplateContainer;
use EcampCore\Entity\EventType;
use EcampCore\Entity\EventTypePlugin;
use EcampCore\Entity\Medium;

class EventTemplatesFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const LAGERSPORT_WEB = 'eventtemplate-lagersport-web';
    const LAGERAKTIVITAET_WEB = 'eventtemplate-lageraktivitaet-web';
    const LAGERPROGRAMM_WEB = 'eventtemplate-lagerprogramm-web';

    /*   AKTUELL NICHT IMPLEMENTIERT

    const LAGERSPORT_MOBILE = 'eventtemplate-lagersport-mobile';
    const LAGERAKTIVITAET_MOBILE = 'eventtemplate-lageraktivitaet-mobile';
    const LAGERPROGRAMM_MOBILE = 'eventtemplate-lagerprogramm-mobile';

    const LAGERSPORT_PRINT = 'eventtemplate-lagersport-print';
    const LAGERAKTIVITAET_PRINT = 'eventtemplate-lageraktivitaet-print';
    const LAGERPROGRAMM_PRINT = 'eventtemplate-lagerprogramm-print';

    */

    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'eventType' => EventTypeFixture::LAGERSPORT,
                'medium' => MediumFixture::MEDIUM_WEB,
                'template' => 'ecamp-web/event-templates/lagersport/index',
                'containers' => array(
                    array(
                        'name' => 'Storyboard',
                        'eventTypePlugin' => EventTypePluginFixture::LAGERSPORT_STORYBOARD,
                        'template' => 'ecamp-web/event-templates/containers/linear'
                    ),
                    array(
                        'name' => 'Material',
                        'eventTypePlugin' => EventTypePluginFixture::LAGERSPORT_MATERIAL,
                        'template' => 'ecamp-web/event-templates/containers/tabs'
                    )
                ),
                'reference' => self::LAGERSPORT_WEB
            ),
            array(
                'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                'medium' => MediumFixture::MEDIUM_WEB,
                'template' => 'ecamp-web/event-templates/lageraktivitaet/index',
                'containers' => array(
                    array(
                        'name' => 'Storyboard',
                        'eventTypePlugin' => EventTypePluginFixture::LAGERAKTIVITAET_STORYBOARD,
                        'template' => 'ecamp-web/event-templates/containers/linear'
                    ),
                    array(
                        'name' => 'Material',
                        'eventTypePlugin' => EventTypePluginFixture::LAGERAKTIVITAET_MATERIAL,
                        'template' => 'ecamp-web/event-templates/containers/tabs'
                    )
                ),
                'reference' => self::LAGERAKTIVITAET_WEB
            ),
            array(
                'eventType' => EventTypeFixture::LAGERPROGRAMM,
                'medium' => MediumFixture::MEDIUM_WEB,
                'template' => 'ecamp-web/event-templates/lagerprogramm/index',
                'containers' => array(
                    array(
                        'name' => 'Storyboard',
                        'eventTypePlugin' => EventTypePluginFixture::LAGERPROGRAMM_STORYBOARD,
                        'template' => 'ecamp-web/event-templates/containers/linear'
                    ),
                    array(
                        'name' => 'Material',
                        'eventTypePlugin' => EventTypePluginFixture::LAGERPROGRAMM_MATERIAL,
                        'template' => 'ecamp-web/event-templates/containers/tabs'
                    )
                ),
                'reference' => self::LAGERPROGRAMM_WEB
            )
        ));
    }


    private function load_(ObjectManager $manager, array $config)
    {
        $eventTemplateRepo = $manager->getRepository('EcampCore\Entity\EventTemplate');
        $eventTemplateContainerRepo = $manager->getRepository('EcampCore\Entity\EventTemplateContainer');

        foreach ($config as $eventTemplateConfig) {
            /** @var EventType $eventType */
            $eventType = $this->getReference($eventTemplateConfig['eventType']);
            /** @var Medium $medium */
            $medium = $this->getReference($eventTemplateConfig['medium']);
            $template = $eventTemplateConfig['template'];
            $containers = $eventTemplateConfig['containers'];
            $reference = $eventTemplateConfig['reference'];

            /** @var EventTemplate $eventTemplate */
            $eventTemplate = $eventTemplateRepo->findOneBy(array(
                'eventType' => $eventType,
                'medium' => $medium
            ));

            if($eventTemplate == null){
                $eventTemplate = new EventTemplate($eventType, $medium, $template);
                $manager->persist($eventTemplate);
            } else {
                $eventTemplate->setFilename($template);
            }


            foreach ($containers as $container) {
                /** @var EventTypePlugin $eventTypePlugin */
                $eventTypePlugin = $this->getReference($container['eventTypePlugin']);
                $containerName = $container['name'];
                $containerTemplate = $container['template'];

                /** @var EventTemplateContainer $eventTemplateContainer */
                $eventTemplateContainer = $eventTemplateContainerRepo->findOneBy(array(
                    'eventTemplate' => $eventTemplate,
                    'containerName' => $containerName
                ));

                if($eventTemplateContainer == null){
                    $eventTemplateContainer = new EventTemplateContainer($eventTemplate, $eventTypePlugin, $containerName, $containerTemplate);
                    $manager->persist($eventTemplateContainer);
                } else {
                    $eventTemplateContainer->setEventTypePlugin($eventTypePlugin);
                    $eventTemplateContainer->setFilename($containerTemplate);
                }
            }

            $this->addReference($reference, $eventTemplate);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 12;
    }
}