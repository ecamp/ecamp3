<?php



set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../../library'),
    get_include_path(),
)));


require_once 'PHPUnit/Autoload.php';


$configFile = __DIR__ . "/../../tests/phpunit.xml";
$resultFile = __DIR__ . "/../../tests/results.xml";




class SilentTestListener
	extends PHPUnit_Util_Printer
	implements PHPUnit_Framework_TestListener
{
    public function __construct($filename = ""){}

    public function flush() {}

    public function incrementalFlush() {}

    public function write($buffer) {}

    public function getAutoFlush(){}

    public function setAutoFlush($autoFlush) {}

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time){}

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time){}

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time){}

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time){}

    public function startTest(PHPUnit_Framework_Test $test) {}

    public function endTest(PHPUnit_Framework_Test $test, $time) {}

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {}

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {}
}


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
        $testResult = new PHPUnit_Framework_TestResult();
        $testResult->addListener($listener);




		$arguments['printer'] = new SilentTestListener();




		$runner = new PHPUnit_TextUI_TestRunner();
		$runner->doRun($test, $arguments);




		// Run the TestSuite
        $result = $test->run($testResult);

        // Get the results from the listener
        $xml_result = $listener->getXML();

		return $xml_result;


		$doConvertErrorToExceptions = false;


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



PHP_CodeCoverage_Filter::getInstance()
		->addDirectoryToBlacklist(__DIR__, '.php', '', 'PHPUNIT');


$result_xml = UnitTestRunner::main($configFile);
file_put_contents($resultFile, $result_xml);


header('Location: index.php');
die();
