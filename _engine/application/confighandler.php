<?php
	
	class ConfigHandler extends Base{
		
		private $_configObject 	= null,
				$_configFile 	= null;
		
		public function __construct( $configObject ){
		
			// Run construct of parent class for to reference methods had
			parent::__construct();
			
			// Check whether the object was passed in the
			if( is_object( $configObject ) ){				
				$this->_configObject = $configObject;				
				// Find filepah of config file
				$this->_findConfigFile();
			}else{
				// Trigger error
				$this->raiseFatal( 'Only object must be passed to first argument' );
				return false;
			}
		}
		
		public function get( $variable, $default = null ){
			
			// If exists propery in config object then return value, else return default data
			if( property_exists( $this->_configObject, $variable ) ){
				return $this->_configObject->$variable;
			}else{
				// Return default value
				return $default;
			}
			
		}
		
		public function set( $variable = null, $value = null ){
			
			if( (boolean) $variable == false || $value === null ){
				return false;
			}
			
			if( property_exists( $this->_configObject, $variable ) ){
				return $this->_configObject->$variable = $value;
			}else{
				return false;
			}
			
		}
		
		public function saveConfig( $data = array() ){
			
			// Instance of file object
			$file = FSO::File();
			
			// Check if config file is readable
			if( ! is_readable( $this->_configFile ) ){
				$this->raiseFatal( 'Файл конфигурации не может быть прочитан<br />Permissions - '. $file->getPermissions( $this->_configFile, true ) .'<br />'. $this->_configFile );
				return false;
			}
			// Check if config file is readable
			if( ! is_writable( $this->_configFile ) ){
				$this->raiseFatal( 'Файл конфигурации не может быть записан<br />Permissions - '. $file->getPermissions( $this->_configFile, true ) .'<br />'. $this->_configFile );
				return false;
			}
			
			// Get php content to store config file
			$php = self::_buildConfig( $data, $this->_configObject );
			
			// Return state of process
			return $file->put( $this->_configFile, $php );
			
		}
		
		private function _findConfigFile(){		
		
			//get config class name
			$configClassName = get_class( $this->_configObject );
			
			// Try to make instance of FleReflection to get the path to the config file
			try{
				$reflct = FleReflection::getInstance( $configClassName );
			}catch( Exception $e ){
				// Catch exception
				$this->raiseFatal( $e->getMessage() .'&nbsp;'. $e->getFile() .':'. $e->getLine() );
			}
			
			// Have full path to config file
			$fullFilepath = $reflct->getFileName();
			
			if( file_exists( $fullFilepath ) ){
				$this->_configFile = $fullFilepath;
			}
			
		}
		
		static private function _buildConfig( $data = array(), $configObject = null ){
		
			$comment = '/**';
			$comment .= "\n\t". '*' ."\t"	. '@engine'. "\t\t" .'Flexsy';
			$comment .= "\n\t". '*' ."\t"	. '@author'. "\t\t" .'Ivan Gontarenko';
			$comment .= "\n\t". '*' ."\t"	. '@email'. "\t\t" .'vania.gontarenko@gmail.com';
			$comment .= "\n\t". '*' ."\t"	. '@skype'. "\t\t" .'ivan.gontarenko';
			$comment .= "\n\t". '*' ."\t";
			$comment .= "\n\t". '*' ."\t"	. '@update'. "\t\t" . date( 'd-m-Y H:i:s', time() );
			$comment .= "\n\t". '*' ."\t"	. '@host'. "\t\t" .'http://'. $_SERVER['HTTP_HOST'];
			$comment .= "\n\t". '*' ."\t";
			$comment .= "\n\t". '*' ."\t"	. '@file_handler'. "\t\t" . __FILE__;
			$comment .= "\t\n\t"	. '*/' . "\n\n";
			
			$php = '<?php' . "\t\n\n\t";
			$php .= $comment;
			$php .= "\t" . 'class '. get_class( $configObject ) . '{' . "\n\n";
			
			foreach( $data as $variable => $value ){
				$value = addcslashes( $value, '\\\'' );
				$php .= "\t\t" . 'public $'. $variable . "\t" . '= \''. $value .'\';' . "\n\n";
			}
			
			$php .= "\t". '}';
			
			return $php;
		}
		
		static public function getInstance( $configObject ){
		
			static $instances;
			$hash = md5( get_class( $configObject ) );
			
			if( empty( $instances ) ){
				$instances = array();
			}
			
			if( empty( $instances[$hash] ) ){
				$instances[$hash] = new ConfigHandler( $configObject );
			}
			
			return $instances[$hash];
		}
		
	}
