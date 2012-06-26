<?php


// Define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));

// Define application environment
define('APPLICATION_ENV', 'testing');

set_include_path(implode(PATH_SEPARATOR, array(
		realpath(APPLICATION_PATH . '/../library/PHPUnit'),
		get_include_path(),
)));


$configFile = __DIR__ . "/../conf/phpunit.xml";


require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . "/SilentTestListener.php";





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



UnitTestRunner::main($configFile);


echo "Done";

