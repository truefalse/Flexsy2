<?php
	
	// Include bootstrap of Flexsy engine
	include_once '_engine/bootstrap.php';
	
	// Create application object
	$application = Application::getInstance( 'site' );
	
	// Initialize application
	$application->init();
	
	// Dispatch module by route
	$application->route();
	
	// Run, generate all result
	$application->run();
	
	// Print result
	print $application->output();