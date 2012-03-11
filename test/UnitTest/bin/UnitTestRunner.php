<?php


// Define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));


set_include_path(implode(PATH_SEPARATOR, array(
		realpath(APPLICATION_PATH . '/../library/PHPUnit'),
		get_include_path(),
)));


if(! file_exists(APPLICATION_PATH . "/../local.conf.php"))
{
	copy(APPLICATION_PATH . "/../default.conf.php", APPLICATION_PATH . "/../local.conf.php");
	throw new Exception("Set correct values to the file local.conf.php!!!");
}
require_once APPLICATION_PATH . "/../local.conf.php";

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . "/SilentTestListener.php";
require_once __DIR__ . "/SchemaManager.php";

$configFile = __DIR__ . "/../conf/phpunit.xml";
$resultFile = APPLICATION_PATH . "/../data/unittest/results.xml";




class UnitTestRunner
{
    private static function runTest(PHPUnit_Framework_TestSuite $test, $arguments)
	{
		$test->setName('eCamp UnitTests');

		
		global $doConvertErrorToExceptions;
		$doConvertErrorToExceptions = true;


		// Create a xml listener object
        $listener = new PHPUnit_Util_Log_JUnit();

        // Create TestResult object and pass the xml listener to it
        //$testResult = new PHPUnit_Framework_TestResult();
        //$testResult->addListener($listener);




		$arguments['printer'] = new SilentTestListener();
		$arguments['listeners'] = array($listener);




		$runner = new PHPUnit_TextUI_TestRunner();
		$result = $runner->doRun($test, $arguments);




		// Run the TestSuite
        //$result = $test->run($testResult);

        // Get the results from the listener
        $xml_result = $listener->getXML();

        $doConvertErrorToExceptions = false;
        
		return $xml_result;
    }


	public static function main($configFile)
	{
		$arguments = array(
            'listGroups' 				=> FALSE,
            'loader' 					=> NULL,
            'useDefaultConfiguration'	=> TRUE,
			'configuration' 			=> $configFile
        );


		$configuration = PHPUnit_Util_Configuration::getInstance($configFile);

		$configuration->handlePHPConfiguration();

		$phpunit = $configuration->getPHPUnitConfiguration();



		if (isset($phpunit['bootstrap']))
		{
			PHPUnit_Util_Fileloader::load($phpunit['bootstrap']);
		}


		$testSuite = $configuration->getTestSuiteConfiguration();

		return self::runTest($testSuite, $arguments);
	}
}



//PHP_CodeCoverage_Filter::getInstance()
//	->addDirectoryToBlacklist(__DIR__, '.php', '', 'PHPUNIT');


$result_xml = UnitTestRunner::main($configFile);
//file_put_contents($resultFile, $result_xml);


echo "Done";

