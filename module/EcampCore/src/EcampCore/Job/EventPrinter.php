<?php

/**
 * Print single Event with wkhtmltopdf
 * 
 * Resque job that has no Application dependencies (standalone job)
 */

define("__BASE__" , dirname(dirname(dirname(dirname(dirname(__DIR__))))) );
require_once __BASE__.'/config/resque.local.php';

class EventPrinter extends BaseJob {
	
	public function printSingleEvent(){
		
		$eventId = $this->args['eventId'];
		$token = $this->job->payload['id'];
	
		$command = "wkhtmltopdf --print-media-type ".__BASE_URL__."/web/group/PBS/TestCamp/event/printJobGenerate?eventId=$eventId ".__DATA__."/printer/$token.pdf";
		
		echo shell_exec($command);
	}

}