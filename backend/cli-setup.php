<?php

require_once __DIR__ . '/autoload.php';

if (PHP_SAPI != 'cli') {
    echo "cli-setup.php sollte in CommandLine ausgeführt werden:";
    echo "<br />";
    echo "<pre>";
    echo "php cli-setup.php";
    echo "</pre>";
    die();
}


$app = eCampApp::CreateSetup();

$sm = $app->getServiceManager();
/** @var \Doctrine\ORM\EntityManager $em */
$em = $sm->get('doctrine.entitymanager.orm_default');
$conn = $em->getConnection();


//  Database-Connection:
// ======================
try {
    echo PHP_EOL;
    $conn->connect();
    echo "1) Database-Connection:  OK";
    echo PHP_EOL;
} catch (\Exception $e) {
    echo "1) Database-Connection:  FAIL";
    echo PHP_EOL . PHP_EOL;
    echo $e->getMessage();
    echo PHP_EOL . PHP_EOL;
    die();
}


//  Schema-Validation:
// ====================
try {
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
    $allMetadata = $em->getMetadataFactory()->getAllMetadata();

    $updateSqls = $schemaTool->getUpdateSchemaSql($allMetadata, true);
    if (count($updateSqls) !== 0) {
        $msg = "Some tables are out of sync.";
        $msg .= PHP_EOL;
        $msg .= "Schema-Update required.";
        $msg .= PHP_EOL;
        $msg .= "Run: vendor/bin/doctrine";
        throw new Exception($msg);
    }
    echo "2) Schema-Validation:    OK";
    echo PHP_EOL;
} catch (\Exception $e) {
    echo "2) Schema-Validation:    FAIL";
    echo PHP_EOL . PHP_EOL;
    echo $e->getMessage();
    echo PHP_EOL . PHP_EOL;
    die();
}


//  Prod-Data-Loading:
// ====================
try {
    echo "3) Prod-Data-Loading:";
    echo PHP_EOL;

    $loader = new \Doctrine\Common\DataFixtures\Loader();
    $paths = \Laminas\Stdlib\Glob::glob(__DIR__ . "/module/*/data/prod/*.php");

    foreach ($paths as $path) {
        echo "    # " . $path . PHP_EOL;
        $loader->loadFromFile($path);
    }

    $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
        $em, new \Doctrine\Common\DataFixtures\Purger\ORMPurger()
    );
    $executor->execute($loader->getFixtures(), true);

    echo "   Prod-Data-Loading:    OK";
    echo PHP_EOL;
} catch (\Exception $e) {
    echo "   Prod-Data-Loading:    FAIL";
    echo PHP_EOL . PHP_EOL;
    echo $e->getMessage();
    echo PHP_EOL . PHP_EOL;
    die();
}



if (in_array('dev', $argv)) {

    //  Dev-Data-Loading:
    // ===================
    try {
        echo "4) Dev-Data-Loading:";
        echo PHP_EOL;

        $loader = new \Doctrine\Common\DataFixtures\Loader();
        $paths = \Laminas\Stdlib\Glob::glob(__DIR__ . "/module/*/data/dev/*.php");

        foreach ($paths as $path) {
            echo "    # " . $path . PHP_EOL;
            $loader->loadFromFile($path);
        }

        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
            $em, new \Doctrine\Common\DataFixtures\Purger\ORMPurger()
        );
        $executor->execute($loader->getFixtures(), true);

        echo "   Dev-Data-Loading:     OK";
        echo PHP_EOL;
    } catch (\Exception $e) {
        echo "   Dev-Data-Loading:     FAIL";
        echo PHP_EOL . PHP_EOL;
        echo $e->getMessage();
        echo PHP_EOL . PHP_EOL;
        die();
    }
}

echo PHP_EOL;
