<?php
	
	// Include system constans
	include_once 'constants.php';
	
	// Include FleReflaction
	include_once ENGINE_PATH . DS . 'default' . DS . 'flereflection.php';
	
	// Include Singleton class
	include_once ENGINE_PATH . DS . 'base' . DS . 'singleton.php';
	
	// Include Loader
	include_once ENGINE_PATH . DS .'loader.php';
	$loader = Loader::getInstance();
	
	// Include main Config File
	include_once ROOT_PATH . DS . 'config.php';
	$config = new Config;
	
	// Define some constants
	define( 'DEBUG', (boolean) $config->debug );
	
	// Set error reporting level
	error_reporting( (int) $config->err_reporting );
	
	// Registry instance
	$registry = Registry::getInstance();
	
	// Attach loader to registry
	$registry->loader 	= $loader;
	
	// Create session
	$registry->session 	= Session::getInstance();
	
	// Create error handler
	$registry->error 	= Error::getInstance();
	
	// Create error handler
	$registry->message 	= Message::getInstance();
	
	// Include debug if enable if config file
	if( (boolean) DEBUG === true ){
		$registry->loader->loadClass( 'debug' );
	}
	
	// Register error method for detect PHP errors
	ShutdownApp::getInstance()->registerShutdown(array(
		$registry->error,
		'detectPHPError'
	));
	
	BaseStatic::set_error_handler( $registry->error );
	BaseStatic::set_loader( $registry->loader );
	
	$registry->config = ConfigHandler::getInstance( $config );
	
	// Kernel object create
	$registry->kernel = Kernel::getInstance();
	
	// DB object
	$registry->db = $registry->kernel->factory( 'dbo' );
	
	require_once '_.php';
	// Include app functions
	include_once ENGINE_PATH . DS .'functions.php';
	