<?php

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

	chdir(dirname(__DIR__));
	
	ini_set('display_errors', true);
	error_reporting((E_ALL | E_STRICT) ^ E_NOTICE);
	
	// Setup autoloading
	require 'init_autoloader.php';
	
	$token = $_GET["token"];
	
	$status = new Resque_Job_Status($token);
	
	if(!$status->isTracking()) {
		die("null");
	}
	
	switch($status->get()){
		case Resque_Job_Status::STATUS_WAITING:
			echo "waiting";
			break;
		case Resque_Job_Status::STATUS_FAILED:
			echo "failed";
			break;
		case Resque_Job_Status::STATUS_RUNNING:
			echo "running";
			break;
		case Resque_Job_Status::STATUS_COMPLETE:
			echo "complete";
			break;
		default:
			echo "null";	
	}

	
	