<?php

	class Application extends Base{
		
		static private		$_instances 	= array();
		
		static  protected	$_client 		= null;
		
		protected 			$_registry		 = null,
							$_cfg			 = null,
							$_output		 = null,
							$_module		 = null,
							$_language		 = null,
							$_route_vars	 = null,
							$_event			 = null,
							$_access		 = null;
		
		function __construct(){
			// Attach registry object
			$this->_registry 	= Registry::getInstance();
			// Attach config to application object
			$this->_cfg 		= $this->_registry->kernel->getConfig();
			// Set deafult headers
			Header::setHeader( 'X-Powered-By', ENGINE . ' ' . VERSION . ' ('. STATUS .')' );
			Header::setHeader( 'X-Created-By', AUTHOR );			
		}
		
		public function init(){
			
			if( $this->_cfg->get( 'site_offline', false ) && self::isFrontend() ){
				$offline_message = str_replace( '{site_name}', $this->_cfg->get( 'site_name' ), $this->_cfg->get( 'site_offline_message', '' ) );
				die( $offline_message );
			}
			
			if( (boolean) $this->_cfg->get( 'handle_route', false ) ){
				$routeLoader = clone $this->_registry->loader;
				$routeLoader->addDirectory( 'application/router' );
				$routeLoader->loadClass( 'routesef' );
			}

			// Attach router to registry
			$this->_registry->router 	= Router::getInstance();	
			// Create instance of access object
			$this->_access = Access::getInstance();
			// Attach user to registry
			$this->_registry->user 		= User::getInstance();
			// Create instance of event object
			$this->_event = Event::getInstance();
			// Load plugin helper
			$pluginLoader = clone $this->_registry->loader;
			$pluginLoader->addDirectory( 'application/plugin' );
			$pluginLoader->loadClass( 'helper' );
			// Import all plugins for trigger on events
			PluginHelper::import( $this->_event );
			// Trigger onApplicationInit event
			$this->_event->trigger( 'onApplicationInit', array() );
			// Create instance of lang and output object
			$this->_language 	= Language::getInstance();			
			$this->_output 		= Output::getInstance();
			
			// Attach to registry
			$this->_registry->event 	= $this->_event;
			$this->_registry->access 	= $this->_access;
			$this->_registry->language 	= $this->_language;
			$this->_registry->output 	= $this->_output;
			$this->_registry->request 	= Request::getInstance();
			// Set default title
			$this->_output->setTitle( $this->_cfg->get('site_name') );			
			// Set default template
			$this->_output->setTemplate( $this->_cfg->get('template') );
			// Get lang from request
			$langFromRequest = $this->_registry->request->getVar( 'lang' );
			// And try set user language
			if( $langFromRequest ){
				$this->_language->setLanguage( $langFromRequest );
			}
			// Load language
			$this->_language->load();
		} 
		
		public function route(){

			$routeString 	= $this->_registry->request->getVar( 'route' );
			// Trigger onRouteApp event
			$this->_event->trigger( 'onRouteApp', array( & $routeString ) );
			// Create instance router
			$router = Router::getInstance( $routeString );
			$this->_route_vars = $router->getVars();
			
		}
		
		public function run(){			
			
			$mvcLoader = clone $this->_registry->loader;
			// Add path where location MVC base files
			$mvcLoader->addDirectory( 'base/mvc' );
			$mvcLoader->addDirectory( 'base/mvc/mvc' );
			// Load MVC files
			$mvcLoader->loadClass( 'controller' );
			$mvcLoader->loadClass( 'model' );
			$mvcLoader->loadClass( 'view' );
			$mvcLoader->loadClass( 'controllerhelper' );
			$mvcLoader->loadClass( 'modelhelper' );
			// Attach to registry menu object
			$this->_registry->menu = Menu::getInstance();
			// Get from request route string
			$routeString 	= $this->_registry->request->getVar( 'route' );
			// Render main module by route string
			$bodyContent 	= Module::render( $routeString );
			// Assign module result to output object
			$this->_output->assign( 'module', $bodyContent );
		}
		
		public function output(){
			$this->_output->assign( 'execution_time', round( microtime( true ) - TIME_START, 4) );
			$this->_event->trigger( 'beforeOutput', array( $this->_output ) );
			
			Header::sendHeaders();
			
			return $this->_output->render_tmpl( true );
		}
		
		static function isFrontend(){
			return ( self::$_client == 'site' );
		}
		
		static function isBackend(){
			return ( self::$_client == 'admin' );
		}
		
		static function getInstance( $applicationName ){			
			// Get instance of application
			if(empty(self::$_instances[$applicationName])){				
				// Application class name
				$className = ucfirst( $applicationName ) . 'Application';
				// Registry object
				$registry = Registry::getInstance();
				// Include application file
				$appLoader = clone $registry->loader;
				$appLoader->addDirectory( 'application/application' );
				$appLoader->loadClass( $applicationName );
				// Create allication object
				self::$_instances[$applicationName] = $registry->kernel->factory( $className );	
			}			
			return self::$_instances[$applicationName];
		}
		
	}
