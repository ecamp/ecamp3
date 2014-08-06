<?php

return array(
    'factories' => array(
        'url' => function ($helperPluginManager) {
            $serviceLocator = $helperPluginManager->getServiceLocator();
            $urlHelper =  new \EcampWeb\View\Helper\BaseUrl();
            $urlHelper->setRouter($serviceLocator->get('HttpRouter'));

            $match = $serviceLocator->get('application')
                ->getMvcEvent()
                ->getRouteMatch();

            if ($match instanceof \Zend\Mvc\Router\Http\RouteMatch) {
                $urlHelper->setRouteMatch($match);
            }

            return $urlHelper;
        },

        'userUrl' => function($helperPluginManager){
            return new \EcampWeb\View\Helper\UserUrl($helperPluginManager->get('url'));
        },

        'campUrl' => function($helperPluginManager){
            return new \EcampWeb\View\Helper\CampUrl($helperPluginManager->get('url'));
        },

        'groupUrl' => function($helperPluginManager){
            return new \EcampWeb\View\Helper\GroupUrl($helperPluginManager->get('url'));
        },

        'membership' => function($helperPluginManager){
            $serviceLocator = $helperPluginManager->getServiceLocator();
            $acl = $serviceLocator->get('EcampCore\Acl');
            $userRepository = $serviceLocator->get('EcampCore\Repository\User');
            $groupMembershipRepository = $serviceLocator->get('EcampCore\Repository\GroupMembership');
            $renderer = $serviceLocator->get('ZfcTwigRenderer');

            return new \EcampWeb\View\Helper\Membership($acl, $userRepository, $groupMembershipRepository, $renderer);
        },

        'collaboration' => function($helperPluginManager){
            $serviceLocator = $helperPluginManager->getServiceLocator();
            $acl = $serviceLocator->get('EcampCore\Acl');
            $userRepository = $serviceLocator->get('EcampCore\Repository\User');
            $campCollaborationRepository = $serviceLocator->get('EcampCore\Repository\CampCollaboration');
            $renderer = $serviceLocator->get('ZfcTwigRenderer');

            return new \EcampWeb\View\Helper\Collaboration($acl, $userRepository, $campCollaborationRepository, $renderer);
        },

        'collaborationDesc' => function($helperPluginManager){
            $serviceLocator = $helperPluginManager->getServiceLocator();
            $acl = $serviceLocator->get('EcampCore\Acl');
            $userRepository = $serviceLocator->get('EcampCore\Repository\User');
            $campCollaborationRepository = $serviceLocator->get('EcampCore\Repository\CampCollaboration');

            return new \EcampWeb\View\Helper\CollaborationDesc($acl, $userRepository, $campCollaborationRepository);
        },
    )
);
