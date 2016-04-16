<?php
return array(

    'invokables' => array(
        'CreatePdf'                     => 'EcampCore\Job\CreatePdfJobFactory',
        'SendActivationMail'            => 'EcampCore\Job\SendActivationMailJobFactory',
        'SendEmailVerificationEmail'    => 'EcampCore\Job\Mail\SendEmailVerificationEmailJobFactory'
    )
);
