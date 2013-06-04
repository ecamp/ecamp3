<?php
return array(

    'aliases' => array(
        '__services__.ecampStoryboard_SectionService' => 'ecampstoryboard.service.section',

        '__internal_services__.ecampStoryboard_SectionService' => 'ecampstoryboard.internal.service.section',
    ),

    'factories' => array(
        'ecampstoryboard.service.section' => new EcampLib\Service\ServiceFactory('ecampstoryboard.internal.service.section'),
    ),

    'invokables' => array(
        'ecampstoryboard.internal.service.section' => 'EcampStoryboard\Service\SectionService',
    ),

);
