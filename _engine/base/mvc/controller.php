<?php

	abstract class Controller extends Base{

		static private $_models = array();

		protected 	$_module_name		= null,
					$_controller_name	= null,
					$_base_path 		= null,
					$_view 				= null,
					$_params 			= null,
					$_request 			= null,
					$_context			= null;

		public function __construct( & $conf){
			parent::__construct();

			self::$_models 			= array();

			$this->_base_path 		= $conf['base_path'];
			$this->_name 			= $conf['name'];
			$this->_page 			= $conf['page'];
			$this->_context 		= ( Application::isFrontend() ? 'site' : 'admin' ) .'.'. $this->_name .'.'. $this->_page;
			
			$this->setParams( $conf['params'] );

			$this->_request 		= Registry::getInstance()->request;
			$this->_router	 		= Router::getInstance();

			$this->loadModel();

			$this->_view = new View($conf);
		}

		public function render( $format ){
			return $this->_view->render( $format );
		}

		private function setParams( $params ){
			$this->_params = $params;
		}

		protected function getModel( $name = null ){
			if( empty( $name ) ){
				$name = $this->_page;
			}
			return ModelHelper::get( $name );
		}

		protected function getHelper( $name = null ){
			return ControllerHelper::getHelper( $this->_module_name, $name );
		}

		protected function loadModel( $page = null, $module = null ){

			$page 		= empty( $page ) 	? $this->_page	 	: $page;
			$module 	= empty( $module ) 	? $this->_name 		: $module;
			
			ModelHelper::addIncludePath( Module::moduleBasePath( $module ) );
			ModelHelper::add( $page );

		}

		protected function setLayout($name = null){
			$this->_view->setLayout($name);
		}

		protected function callModule($route = null, $config = null){
			$module = new Module;
			$module->new_module($route, $config);
			return $module->call();
		}

		abstract public function index();

	}
