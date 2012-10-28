<?php
	
	
	class PluginHelper extends BaseStatic{
		
		static private $_plugins = array();
		
		static public function import( Event $event ){
		
			$plugins = & self::_load_plugins();
			
			if( ! empty( $plugins ) ){
			
				foreach($plugins as $plugin){
					self::_include_file($plugin);
					$plugin->details = json_decode( $plugin->details );
					self::$_plugins[] = self::_get_plugin($plugin);
				}
				
			}
			
			foreach(self::$_plugins as $plugin){
				$event->register($plugin);
			}
			
		}
		
		static private function & _load_plugins(){
			
			$where = array( 'p.`enable` >= 1' );
			
			if( Application::isFrontend() ){
				$where[] = 'p.`frontend` = 1';
			}else if( Application::isBackend() ){
				$where[] = 'p.`backend` = 1';
			}
			
			$query = 'SELECT * FROM #P_plugins AS p WHERE ' . implode( ' AND ', $where );
			
			return Registry::getInstance()->db->objectList( $query );
		}
		
		static private function _include_file( & $plugin ){
			$name = preg_replace('/[^a-z0-9_\.-]/i', '', $plugin->name);
			$file = preg_replace('/[^a-z0-9_\.-]/i', '', $plugin->file);
			
			$plugin_file = PLUGINS_PATH . DS . $name . DS . $file;
			
			if( ! file_exists( $plugin_file ) ){
				parent::$_error->raise('Plugin file not find <br /><b>'.$plugin_file.'</b>', 'fatal');
			}
			
			include_once $plugin_file;			
		}
		
		static private function _get_plugin( $plugin ){
			$class_name = ucfirst( strtolower( $plugin->name ) ) .'Plugin';
			return new $class_name($plugin);
		}
		
	}
