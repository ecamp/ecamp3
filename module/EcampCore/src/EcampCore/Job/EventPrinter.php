<?php

/**
 * Dummy action for a resque job that has no Application dependencies (standalone job)
 *
 */

class EventPrinter extends BaseJob {
	
	public function printSingleEvent(){
		
		var_dump($this->args);
	}

}