<?php
return  array(
    'type' => 'Literal',
    'may_terminate' => false,
    'options' => array(
        'route' => '/resque'
    ),

    'child_routes' => array(

        'workers' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '/workers[/:worker]',
                'defaults' => array(
                    'controller' => 'Resource\Resque\Worker'
                )
            )
        ),

        'jobs' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '/jobs[/:job]',
                'defaults' => array(
                    'controller' => 'Resource\Resque\Job'
                )
            )
        )

    )
);
