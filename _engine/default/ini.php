<?php
	
    
	class INI extends Base{
		
        static private $string = array();
        static private $object = array();
        
        static public function & toObject( $string = null ){
            
            $string = trim( $string );
            $hash = md5( $string );
            
            if( is_object( self::$object[$hash] ) )
                return self::$object[$hash];
                
            $lines = explode( "\n", $string );  
                      
            $count = count( $lines );
            
            $output = new BlankObject;
            $object =& $output;
            for( $i=0; $i<$count; $i++ ){
                
                $line =& $lines[$i];
                $line = trim( $line );
                
                $strlen = strlen($line);
                
                if( $line{0} == ';' )
                    continue;
                else if( $line{0} == '[' && $line{$strlen-1} == ']' ){
                    $block = substr( $line, 1, $strlen-2 );
                    $output->$block = new BlankObject;
                    $object =& $output->$block;
                    continue;
                }else{
                    
                    $pos = strpos( $line, '=' );   
                                   
                    $param = substr( $line, 0, $pos );
                    $value = substr( $line, $pos+1 );
                    
                    $value = stripslashes( $value );
                    
                    $valueLen = strlen( $value );
                    
                    if( 
                        $value{0} && $value{$valueLen-1} == '"' || 
                        $value{0} && $value{$valueLen-1} == '\'' 
                    )
                        $value = substr( $value, 1, $valueLen-2 );
                    
                    if( !! $param{0} )
                        $object->$param = $value;
                }
            }
            
            self::$object[$hash] =& $output;
            
            return $output;
            
        }
        
        static public function & toString( $object = null ){
            
            if( ! is_array( $object ) && ! is_object( $object ) )
                return false;
            
            $ini_string = null;
            
            if( is_object( $object ) || is_array( $object ) ){
                if( is_object( $object ) )
                    $object = get_object_vars( $object );
                foreach( $object as $key => $value ){
                    if( is_object( $value ) || is_array( $value ) ){
                        $ini_string .= '[' . $key . ']' . "\n";
                        if( is_object( $value ) )
                            $value = get_object_vars( $value );
                        foreach( $value as $k => $v )
                            $ini_string .= $k . '=' . addslashes( (string) $v ) . "\n";
                    }else{
                        $ini_string .= $key . '=' . addslashes( (string) $value ) . "\n";
                    }                    
                }
            }  
             
            return $ini_string;  
                   
        }
        
	}
	
?>
