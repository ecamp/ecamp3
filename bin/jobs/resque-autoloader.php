<?php

/**
 * Autoloader for Resque Job classes
*
*/
class Ecamp_Job_Autoloader
{
	/**
	 * Registers Ecamp_Job_Autoloader as an SPL autoloader.
	 *
	 * @param Boolean $prepend Whether to prepend the autoloader or not.
	 */
	public static function register($prepend = false)
	{
		if (version_compare(phpversion(), '5.3.0', '>=')) {
			spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
		} else {
			spl_autoload_register(array(__CLASS__, 'autoload'));
		}
	}

	/**
	 * Handles autoloading of classes.
	 *
	 * @param string $class A class name.
	 */
	public static function autoload($class)
	{
		/*
		if (0 !== strpos($class, 'Ecamp')) {
			return;
		}*/

		/* Autoloader for job classes with no depencies to ZF2 (standalone jobs) */
		if (is_file($file = __DIR__.'/../../module/EcampCore/src/EcampCore/Job/'.$class.'.php')) {
			require $file;
			return;
		}
		
		/* Autoloader for CLI jobs (through Zf2Cli.php */
		if (is_file($file = __DIR__.'/'.$class.'.php')) {
			require $file;
			return;
		}
	}
}

$autoloader = new Ecamp_Job_Autoloader();
$autoloader->register(false);

