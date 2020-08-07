<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

$connected = false;
while (!$connected) {
    try {
        $app = \eCampApp::CreateSetup();
        $sm = $app->getServiceManager();
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $sm->get('doctrine.entitymanager.orm_default');
        $conn = $em->getConnection();
        $conn->connect();
        $connected = true;
        echo "DB is online\n";
        $conn->close();
    } catch (\Exception $e) {
        echo "Waiting for DB to come online...\n";
        echo $e->getMessage();
        echo "\n";
        sleep(5);
    }
}
