<?php

	class Module extends BaseStatic{
		
		static private 	$_objects = array(),
						$_params = array();
		
		static public function render( $routeString ){
		
			$hash = md5( $routeString );
			
			// Get router instance
			$router = Router::getInstance( $routeString );
			$vars 	= $router->getVars();
			
			// If does not exist, create a new instance
			if( ! ( self::$_objects[$hash] instanceOf Controller ) ){
				self::$_objects[$hash] = self::_create( $vars );
			}
			
			// Run module
			if( method_exists( self::$_objects[$hash], $vars['action'] ) ){			
				// Module result
				$result = call_user_method( $vars['action'], self::$_objects[$hash] );
			}else{			
				// Error because method was not found
				parent::$_error->raise( 'Page not found', 'fatal' );
				return;
			}
			
			if( empty( $result ) ){
				// If the method does not return, we render template of controller
				$result = self::$_objects[$hash]->render( $vars['format'] );
			}
			
			return $result;
			
		}
		
		static public function getParams( $module, $page ){
			
			$hash = md5( $module . $page . self::conf( 'salt' ) );
			$key = 'module.' . $module;
			
			if( ! is_object( self::$_params[$hash] ) ){
				Params::set_include_path( MOLULES_PATH . DS . $module );
				self::$_params[$hash] = Params::load( $module );				
			}
			
			return self::$_params[$hash]->get( $key, new BlankObject );
		}
		
		static public function moduleBasePath( $module_name = null ){
			return MOLULES_PATH . DS . strtolower( $module_name );
		}
		
		static private function _create( array $route ){
			
			$module_name 	= $route['module'];
			$format 		= $route['format'];
			$page	 		= $route['controller'];
			$action 		= $route['action'];
			
			# --- Check if register this module
			$db = Registry::getInstance()->db;
			$where = array( 'm.enable = 1', 'm.name = \''. $module_name .'\'' );
			
			if(Application::isFrontend()){
				$where[] = 'm.frontend = 1';
			}else if(Application::isBackend()){
				$where[] = 'm.backend = 1';
			}
			
			$query = 'SELECT * FROM `#P_modules` AS m WHERE ( '. join( ' AND ', $where) .' ) LIMIT 1';
			
			$row = $db->objectItem($query);
			
			if( empty( $row ) ){
				parent::$_error->raise( 'Sorry but module \''. strtolower( $module_name ) .'\' not registered', 404 );
				return new stdClass;;
			}
			
			# --- Enter point of module			
			$enter_point = MOLULES_PATH;
			$enter_point .= DS . strtolower( $module_name ) . DS . 'index.php';
			
			# --- Check file
			if( ! file_exists( $enter_point ) ){
				$this->errorMessage( 'Entry point of \''. strtolower( $module_name ) .'\' not found' );
			}
			
			# --- Include enter point
			include_once $enter_point;
			
			# --- Load language file
			$module_lang_file = 'module.' . $module_name;
			
			Registry::getInstance()->language->load($module_lang_file);
			
			# --- Find controller
			$module_controller_file = MOLULES_PATH;
			$module_controller_file .= DS . strtolower($module_name) . DS . 'controllers' . DS . strtolower($page) . '.php';
			
			if(!file_exists($module_controller_file)){
				parent::$_error->raise( 'Controller \''. $module_controller_file .'\' not found', 'error' );
				return;
			}else{
				include_once $module_controller_file;	
			}
			
			# --- Make class name of module
			$module_classname = ucfirst(strtolower($module_name)) . ucfirst(strtolower($page));
			
			$params = self::getParams( $module_name, $page );
			
			$config = array(
				'base_path' 	=> self::moduleBasePath( $module_name ),
				'name'			=> $module_name,
				'page'			=> $page,
				'params' 		=> $params
			);
			
			if(!class_exists($module_classname)){
				parent::$_error->raise( 'Class \''. $module_classname .'\' of controller \''. $module_controller_file .'\' not found', 'error' );
				return new stdClass;
			}else{
				return new $module_classname($config);
			}
			
		}
		
	}