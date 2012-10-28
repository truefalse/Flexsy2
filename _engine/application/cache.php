<?php
	
	class Cache extends Base{
		
		private $_fileObject	= null,
				$_dirObject		= null,
				$_cacheFile		= null,
				$_cacheDir		= null,
				$_lifeTime		= 0;
		
		public function __construct( $cacheKey ){
			// Base __construct
			parent::__construct();			
			// Add FSO
			$this->_fileObject 	= FSO::File();
			$this->_dirObject 	= FSO::Folder();
			// Prepare key
			$cacheKey 	= $this->_prepareKey( $cacheKey );
			// Setting
			if( false !== $cacheKey ){
				// Divided by the symbol path
				$keyParts	= explode( '.', $cacheKey );			
				// Make file name
				$this->_makeCacheFile( array_pop( $keyParts ) );
				// Make dir
				$this->_makeCacheDir( $keyParts );
				// Create dir if not exists
				if( ! file_exists( $this->_cacheDir ) ){
					$this->_dirObject->create( $this->_cacheDir );
				}
				
			}
			// Lifetime
			$this->_lifeTime = Kernel::getInstance()->getConfig()->get( 'cache_lifetime', 3600 );
		}
		
		static public function getInstance( $cacheKey = null ){
			
			static $instances;
			
			if( empty( $instances ) ){
				$instances = array();
			}
			
			$hash = md5( $cacheKey );
			
			if( empty( $instances[$hash] ) ){
				$instances[$hash] = new Cache( $cacheKey );
			}
			
			return $instances[$hash];
			
		}
		
		public function get(){
			// Get check result
			$check = $this->check();			
			if( ! $check ){
				// Try to clean cache
				$this->clean();
				return false;
			}else{
				// Try to return cached data			
				return $this->_getCacheData();
			}
			
		}
		
		public function write( $data = null ){
			// Check key mode
			if( $this->_cacheFile == -1 ){
				$this->warningMessage( 'Cache: The key is not supported by this method' );
				return false;
			}
			// Get file content
			$content = $this->_prepareData( $data );
			// Full path to file
			$cacheFile = $this->_cacheDir . DS . $this->_cacheFile;
			// Put to the file
			return $this->_fileObject->put( $cacheFile, $content );
		}
		
		public function check(){
			// Check key mode
			if( $this->_cacheFile == -1 ){
				$this->warningMessage( 'Cache: The key is not supported by this method' );
				return false;
			}			
			// Full path to file
			$cacheFile = $this->_cacheDir . DS . $this->_cacheFile;			
			// Check
			if( ! file_exists( $cacheFile ) ){
				return false;
			}else{
				return ( time() <= ( filemtime( $cacheFile ) + (int) $this->_lifeTime ) );
			}			
		}
		
		public function clean(){
			// One or all files
			if( $this->_cacheFile == -1 ){
				$files = $this->_fileObject->files( $this->_cacheDir, array(), '.', true, true );
				// Delete all files
				foreach( $files as $file ){
					@ unlink( $file );
				}
			}else{
				// Full path to file
				$cacheFile = $this->_cacheDir . DS . $this->_cacheFile;
				return @ unlink( $cacheFile );
			}				
		}
		
		private function _prepareData( & $data ){
			// Cache data
			$cacheData 		= array();
			$cacheContent 	= null;
			// Get data type
			if( is_array( $data ) || is_object( $data ) ){
				$cacheData['content'] = serialize( $data );
			}else{
				$cacheData['content'] = (string) $data;
			}
			$cacheData['contentType'] = gettype( $data );
			// Build cache file content
			$this->_createCacheContent( $cacheContent, $cacheData );
			// Return content
			return $cacheContent;
		}
		
		private function _createCacheContent( & $cacheContent, array & $cacheData ){
			// Add PHP preproc
			$cacheContent = '<?php' . "\n\n";
			// Add comments
			$cacheContent .= "\t" . '# Date:' . "\t" . date( 'd-m-Y H:i:s', time() ) . "\n";
			$cacheContent .= "\t" . '# File:' . "\t" . $this->_cacheFile . "\n";
			$cacheContent .= "\t" . '# Folder:' . "\t" . $this->_cacheDir . "\n\n";
			// Add cache variable
			$cacheContent .= '$_cache = ' . var_export( $cacheData, true ) . ';';
		}
		
		private function _getCacheData(){
			// Full path
			$cacheFile = $this->_cacheDir . DS . $this->_cacheFile;
			// Empty cache variable
			$_cache = array();
			// Require cached file
			require_once $cacheFile;
			// Check variable $_cache
			if( is_array( $_cache ) && isset( $_cache['contentType'] ) && isset( $_cache['content'] ) ){
				$data = null;
				// Check content type
				if( $_cache['contentType'] == 'object' || $_cache['contentType'] == 'array' ){
					$data = @ unserialize( $_cache['content'] );
				}else{
					$data = $_cache['content'];
					settype( $data, $_cache['contentType'] );
				}
				return $data;
			}else{
				return null;
			}
		}
		
		private function _prepareKey( $key = null ){
			// Check key
			if( $key == null || ! (boolean) $key ){
				$this->warningMessage( 'Cache: Key maybe is empty' );
				return false;
			}
			// Prepare key
			$key = trim( $key, '.' );
			$key = preg_replace( '/\.+/', '.', $key );
			// Return key
			return $key;
			
		}
		
		private function _makeCacheDir( $keyParts ){
			// Make dir path
			$dir		= implode( DS, $keyParts );
			$dir		= CACHE_PATH . DS . $dir;			
			// dir name to cache object
			$this->_cacheDir = $dir;	
		}
		
		private function _makeCacheFile( $fileKey ){
			// Make file name
			if( false === strpos( '*', $fileKey ) ){
				$file 	= 'cache-' . md5( $fileKey ) . '.php';
			}else{
				$file 	= -1;
			}
			// file name to cache object
			$this->_cacheFile = $file;
			
		}
		
	}
