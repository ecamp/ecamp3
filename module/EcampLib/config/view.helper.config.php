<?php

return array(
    'factories' => array(
        'show_variables' => function($sm) {
            $helper = new EcampLib\View\Helper\ShowVariables();

            return $helper;
        }
    )

);
