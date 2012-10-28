<?php
	
    
	class FSO{
		
        static private $objects = null;
        
        static public function & File(){
		
            if( ! is_object( self::$objects['_File'] ) && ! ( self::$objects['_File'] instanceof _File ) ){
				self::$objects['_File'] = new _File;
			}
                
            return self::$objects['_File'];
        }
        
        static public function & Folder(){
		
            if( ! is_object( self::$objects['_Folder'] ) && ! ( self::$objects['_Folder'] instanceof _Folder ) ){
				self::$objects['_Folder'] = new _Folder;
			}
                
            return self::$objects['_Folder'];
        }
        
	}
    
    class _File{
		
		public function get( $filePath = '' ){
			return file_get_contents( $filePath );
		}
		
        public function put( $file, $content = '' ){
		
            $dir = dirname( $file );
            $file = basename( $file );
            
            if( ! is_dir( $dir ) && ! file_exists( $dir ) ){
				FSO::Folder()->create( $dir );
			}
                
            $file = $dir . DS . $file;            
            return file_put_contents( $file, (string) $content );
			
        }
        
		public function files( $path, $exclude = array(), $filter = '.', $recursive = false, $fullpath = false ){
		
			$path 		= preg_replace( '/(\/|\\\)+/', DS, $path );
			$path 		= rtrim( $path, DS );			
			$exclude 	= array_merge(array('.', '..'), $exclude);
			$files 		= array();
			
			if( $fh = opendir( $path ) ){
			
				while(false !== ($file = readdir($fh))){
				
					if( !in_array($file, $exclude) ){
					
						if(is_file($path . DS . $file) && preg_match('/'. $filter .'/', $file)){
							$files[] = $fullpath ? $path . DS . $file : $file;
						}else if(is_dir($path . DS . $file) && $recursive == true){
							$files = array_merge($files, self::files( $path . DS . $file, $exclude, $filter, $recursive, $fullpath ));
						}	
						
					}
				}
				
				closedir($fh);
				
			}
						
			return $files;			
		}
		
        public function read( $file ){
				
				$fh = fopen($file, 'a+');
				
				if(!$fh){
					return null;
				}
				
				$filesize = filesize($file);
				
				if(0 >= $filesize){
					return null;
				}
				
				$content = fread( $fh, $filesize );
				fclose($fh);
				
				return $content;
        }
        
		public function ext( $file ){
			return end( explode( '.', $file ) );
		}
		
		public function getPermissions( $file = null, $dec = false ){
			$perm = fileperms( $file );
			
			if( $dec === true ){
				$perm = substr( decoct( $perm ), -4 );
			}
			
			return $perm;
		}
		
	}
    
    class _Folder{
		
		public function folder_list( $path ){
			$path = preg_replace( '/(\/|\\\)+/', DS, $path );
			$path = rtrim($path,DS);
			$folders = array();
			if( $fh = opendir( $path ) ){
				while(false !== ($file = readdir($fh))){
					if(is_dir($path.DS.$file) and !in_array($file, array('.', '..'))){
						$folders[] = $file;
					}
				}
				closedir($fh);
			}
			return $folders;
		}
		
        public function create( $dir ){
            if( is_file( $dir ) )
                $dir = dirname( $dir );
            
            $dir = preg_replace( '/(\/|\\\)+/', DS, $dir );
            
            mkdir( $dir, 0777, true );
        }
        
	}
	
?>
