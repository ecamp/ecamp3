<?php

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
