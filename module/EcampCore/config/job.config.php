<?php
return array(

    'invokables' => array(
        'CreatePdf'                     => 'EcampCore\Job\CreatePdfJobFactory',
    ),

    'aliases' => array(
        'SendEmailVerificationEmail'    => EcampCore\Job\Mail\SendEmailVerificationEmailJobFactory::class,
        'SendPwResetMail'               => EcampCore\Job\Mail\SendPwResetMailJobFactory::class
    ),

    'factories' => array(
        EcampCore\Job\Mail\SendEmailVerificationEmailJobFactory::class  => EcampCore\Job\Mail\SendEmailVerificationEmailJobFactory::class,
        EcampCore\Job\Mail\SendPwResetMailJobFactory::class             => EcampCore\Job\Mail\SendPwResetMailJobFactory::class,
    )
);
