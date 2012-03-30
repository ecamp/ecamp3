<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));




$installerPath 	= "./../install/";
$configFile 	= "./../config.ini";


include_once ($installerPath . 'InstallerBase.php');
include_once ("Zend/Config/Ini.php");
include_once ("Zend/config/Writer/Ini.php");



//  Load InstallerPackages:
// =========================

$dir = opendir($installerPath);
$classes = array();

while($file = readdir($dir))
{
	$i = pathinfo($installerPath . $file);
	$class = $i['filename'];
	
	if($i['extension'] == 'php')
	{	include_once($installerPath . $file);	}
	
	if(class_exists($class) && in_array('InstallerBase', class_parents($class)))
	{	$classes[] = $class;	}
}

closedir($dir);



//  Run InstallerPackages:
// ========================

if(! file_exists($configFile))
{	touch($configFile);	}

$config = new Zend_Config_Ini($configFile, null,
	array('allowModifications' => isset($_REQUEST['install'])));


echo "<h1>Install</h1>";

echo "<form action='/install.php'>";
echo "<input type='submit' value='Refresh' />";
echo "</form>";


foreach($classes as $class)
{
	$package = new $class($config);
	/** @var $package InstallerBase */

	echo "<h3>" . $class . " - Installer</h3>";

	
	if(isset($_REQUEST['install']) && $_REQUEST['install'] == $class)
	{
		$package->Install();
		echo "&nbsp;&nbsp;&nbsp; <i>Just installed...</i>";
	}
	elseif($package->IsInstalled())
	{
		echo "&nbsp;&nbsp;&nbsp; <i>Allready installed...</i>";
	}
	else
	{
		echo "<form action='/install.php' style='background-color: orange'>";
		echo $package->RenderForm();
		echo "<input type='hidden' name='install' value='" . $class . "' />";
		echo "<input type='submit' value='Install' />";
		echo "</form>";
	}
	
}


if(isset($_REQUEST['install']))
{
	$configWriter = new Zend_Config_Writer_Ini();
	$configWriter->setConfig($config);
	$configWriter->write($configFile);
}



