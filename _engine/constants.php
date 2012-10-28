<?php
	
	define( 'TIME_START', microtime(true) );
	
	// Path
	define( 'DS', 						DIRECTORY_SEPARATOR );
	define( 'ROOT_PATH', 				preg_replace( '/\\//', DS, $_SERVER['DOCUMENT_ROOT'] ) );
	define( 'BASE_PATH', 				dirname( preg_replace( '/\\//', DS, $_SERVER['SCRIPT_FILENAME'] ) ) );
	define( 'ENGINE_PATH', 				ROOT_PATH . DS . '_engine' );
	define( 'LIBS_PATH', 				ENGINE_PATH . DS . 'libs' );
	define( 'APP_PATH', 				BASE_PATH . DS . 'application' );
	define( 'ASSETS_PATH', 				BASE_PATH . DS . 'assets' );
	define( 'PLUGINS_PATH', 			APP_PATH . DS . 'plugins' );
	define( 'MOLULES_PATH', 			APP_PATH . DS . 'modules' );	
	define( 'TEMPLATES_PATH', 			ASSETS_PATH . DS . 'templates' );
	define( 'UPLOADS_PATH', 			ASSETS_PATH . DS . 'uploads' );
	define( 'CACHE_PATH', 				ASSETS_PATH . DS . 'cache' );
	define( 'LANG_PATH', 				ASSETS_PATH . DS . 'languages' );
	
	// For frondend side
	define( 'SITE_APP_PATH', 			ROOT_PATH . DS . 'application' );
	define( 'SITE_ASSETS_PATH', 		ROOT_PATH . DS . 'assets' );
	define( 'SITE_PLUGINS_PATH', 		SITE_APP_PATH . DS . 'plugins' );
	
	// Files
	define( 'APP_XML_CONFIG', 			ENGINE_PATH . DS . 'applicationConfig.xml' );
	
	//Info
	define( 'ENGINE', 					'Flesxy' );
	define( 'VERSION', 					'2.0' );
	define( 'HOMEPAGE', 				'flesxy.net' );
	define( 'AUTHOR', 					'Ivan Gontarenko' );
	define( 'AUTHOR_EMAIL', 			'vania.gontarenko@gmail.com' );
	define( 'STATUS', 					'Alpha' );	
