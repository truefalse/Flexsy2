<?php
	
	class Tmpl extends BaseStatic{
		
		private $_data = array();
		private $_conf = array();
		static private $_templates = array();
		
		private function __construct( array $conf = array() ){
		
			$this->_data = & $conf['data'];
			
			$this->_conf = array(
				'tmpl_dir' => URI::base() . 'assets/templates/' . $conf['tmpl_name'] . '/',
				'layout' => $conf['layout']
			);
			
		}
		
		static public function create( array $conf = array() ){
			$hash = md5( $conf['layout'] . $conf['tmpl_name'] );
			
			if( ! empty( self::$_templates[$hash] ) ){
				return self::$_templates[$hash];
			}
			
			self::$_templates[$hash] = new self( $conf );	
			
			return self::$_templates[$hash];			
		}
		
		public function tmpl_path(){
			return $this->_conf['tmpl_dir'];
		}
		
		public function get( $key ){
			return $this->_data[$key];
		}
		
		public function render(){
		
			$useSmarty = Registry::getInstance()->get( 'config' )->get( 'useSmarty' );
			
			return $useSmarty 
						? self::_renderSmarty( $this->_conf['layout'], $this ) 
						: self::_renderSimple( $this->_conf['layout'], $this );
						
		}
		
		static private function _renderSmarty( $tmpl_file, Tmpl $tmpl ){
			
			include_once  LIBS_PATH . DS .'Smarty-3.1.12'. DS .'Smarty.class.php';
			
			$smarty = new Smarty;
			
			$config = Registry::getInstance()->config;
			
			$smarty->template_dir 	 = TEMPLATES_PATH . DS . $config->get( 'template' );
			$smarty->compile_dir 	 = CACHE_PATH . DS . 'compiled';
			$smarty->cache_dir 	 	 = CACHE_PATH;
			
			foreach( $tmpl->_data as $key => $value ){
				$smarty->assign( $key, $value );
			}
			
			$smarty->assign( 'config', 		$config );
			$smarty->assign( 'template', 	$tmpl );
			
			return $smarty->fetch( basename( $tmpl_file ) );
			
		}
		
		static private function _renderSimple( $tmpl_file, Tmpl $tmpl ){
			ob_start();
				include $tmpl_file;
				$output = ob_get_contents();
			ob_clean();
			return $output;		
		}
		
	}