<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

set_time_limit(7200);

$timeFormat = "dd.mm.YYYY HH:mi";

chdir(dirname(__DIR__));

ini_set('display_errors', true);
error_reporting((E_ALL | E_STRICT) ^ E_NOTICE);

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
$app = Zend\Mvc\Application::init(require 'config/job.config.php');

EcampLib\Job\Application::Set($app);




$start = time();
$stop = $start + (60*60); // 1h
$fileToken = __DATA__ . '/tmp/' . $start . '.job';
touch($fileToken);

while($stop > time() && file_exists($fileToken)) {

    /** @var \Resque\Job $job */
    $job = \Resque::pop(array('php-only'));

    if ($job != null) {
        // Log Job-Start
        file_put_contents($fileToken, date($timeFormat) . " # " . $job->getId() . ": Job started" . PHP_EOL, FILE_APPEND);

        if($job->perform()){
            file_put_contents($fileToken, date($timeFormat) . " # " . $job->getId() . " Job succeeded" . PHP_EOL, FILE_APPEND);

            $instance = $job->getInstance();
            if ($instance instanceof \EcampLib\Job\JobResultInterface) {
                file_put_contents($fileToken, date($timeFormat) . " # " . $job->getId() . " Job result: " . $instance->getResult() . PHP_EOL, FILE_APPEND);
            }
        } else {
            file_put_contents($fileToken, date($timeFormat) . " # " . $job->getId() . " Job failed" . PHP_EOL, FILE_APPEND);
        }
    }

}