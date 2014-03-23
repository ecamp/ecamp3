<?php
/**
 * Entry point for Resque jobs of the type
 * 
 * \Resque::enqueue($queue, 'Zf2Cli', array(
 *   			'command' => $command
 *   	));
 *   
 *  where $command is a valid ZF2 CLI command 
 */


/* This makes our life easier when dealing with paths. Everything is relative to the application root now. */
chdir(dirname(__DIR__.'/../../public'));

ini_set('display_errors', true);
error_reporting((E_ALL | E_STRICT) ^ E_NOTICE);

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
class Zf2Cli {
	public function perform() {
		global $argc,$argv;
		$argv = explode(' ',$this->args['command']);
		array_unshift($argv,'Zf2Cli.php');
		$argc = count($argv)+1;
		$_SERVER['argv'] = $argv;
		$_SERVER['argc'] = $argc;
		Zend\Mvc\Application::init(require 'config/application.config.php')->run();
		
		echo "after perform";
	}
}
