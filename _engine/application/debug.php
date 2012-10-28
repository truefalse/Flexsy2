<?php
	
	ini_set( 'highlight.keyword', 	'#C10002' );
	ini_set( 'highlight.string', 	'#008800' );
	ini_set( 'highlight.comment', 	'#989898' );
	ini_set( 'highlight.default', 	'#0000B3' );
	
	class Debug{
		
		static public function highlight($error){
			if(empty($error)) return;
			$out = self::_fetch_line_from_file($error);
			return $out;
		}
		
		static public function highlight_array($errors){
			if(empty($errors)) return;
			$out = '';
			foreach( $errors as $error ){
				$out .= self::highlight($error);
			}
			return $out;
		}
		
		static private function _fetch_line_from_file($error){
			$file = $error['file'];
			if(file_exists($file)){
				if(is_readable($file)){					
					$line = $error['line'];
					
					$php_file_content = file_get_contents($file);
					$lines = preg_split( '/\n/ui', $php_file_content );
					
					$line-=1;
					
					$show_lines 	= 4;
					$count_lines 	= count($lines);
					$start 			= (0>=($line-$show_lines))?0:$line-$show_lines;
					$end 			= ($count_lines<($line+$show_lines))?$count_lines:$line+$show_lines;

					$php_preproc = array('<?php');
					
					$out = '<table style="width:100%;border-bottom:1px solid #BFBFBF;background:#FFFFFF;font-size:12px;" cellspacing="0" cellpadding="0">';	
					
					$info = '<table style="white-space:normal;width:100%;border-bottom:1px solid #BFBFBF;background:#FFFFFF;font-family:courier new;font-size:12px;" cellspacing="0" cellpadding="2">';
					
					$info .= '<tr>';
					$info .= '<td style="width:5px;padding-right:10px;font-weight:bold;">File:</td>';
					$info .= '<td><span style="color:#2E869C; font-weight:bold;">'. basename($file) .'</span><b>:</b><span style="color:#FF0000; font-weight:bold;">'. ($line+1) .'</span></td>';
					$info .= '</tr>';
					
					$info .= '<tr>';
					$info .= '<td style="padding-right:10px;font-weight:bold;">Path:</td>';
					$info .= '<td><span style="color:#2E629C;">'. dirname($file) .'</span></td>';
					$info .= '</tr>';
					
					if(!empty($error['function'])){
						$info .= '<tr>';
						$info .= '<td style="padding-right:10px; font-weight:bold;">Call:</td>';
						$call = null;
						if(!empty($error['class'])){
							if($error['type'] == '->'){
								$call .= '<i><u>Object</u></i> ';
							}
							$call .= '<span style="color:#004283;font-weight:bold;">'. $error['class'] .'</span>';
							$call .= '<span style="color:#2FA38A;">'. $error['type'] .'</span>';
						}
						$call .= '<span style="color:#CB1A66;font-weight:bold;">'. $error['function'] .'</span>';
						
						$call .= '<span style="color:#000;font-weight:bold;">(</span>'
									.self::_build_args($error['args']) 
									.'<span style="color:#000;font-weight:bold;">);</span>';
						
						$info .= '<td><code>'. $call .'</code></td>';
						$info .= '</tr>';
					}
					
					$info .= '</table>';
					
					$out .= '<tr>';
					$out .= '<td colspan="2">'.$info.'</td>';
					$out .= '</tr>';
							
					for( $i=$start; $i<=$end; $i++ ){
						$out .= '<tr>';
						$out .= '<td style="width:5px;background:'.($i==$line?'#a00':'#000').';padding:1px 3px;border-right:1px solid #BFBFBF;color:#FFF;"><code>'.($i+1).'</code></td>';
						$out .= '<td style="background:'.($i%2==0?'#F5F5F5':'#FFFFFF').';padding-left:3px;border-right:1px solid #BFBFBF;">';
						$out .= str_replace('&lt;?php','', highlight_string($php_preproc[0].$lines[$i], true));
						$out .= '</td>';
						$out .= '</tr>';
					}					
					$out .= '</table>';
					
					return self::_tpl( $out  );
				}else{
					return self::_tpl( $file );	
				}
			}			
		}
		
		static private function _dump_array(array $array){
			$out = '<span style="color:#A52A2A;">Array(</span>';
			$tmp = array();
			foreach($array as $k => $v){
				$k = '[<span style="color:#006228;font-weight:bold;">\''. $k .'\'</span>]';
				if(is_array($v)){
					$tmp[] = $k .' => '. self::_dump_array($v);
				}else{
					$tmp[] = $k .' => '. self::_dump_value($v);
				}
			}
			$out .= join(', ', $tmp) . '<span style="color:#A52A2A;">)</span>';
			return $out;
		}
		
		static private function _dump_value($value){
			$out = null;
			if(is_array($value)){
				$out = self::_dump_array($value);
			}else if(is_object($value)){
				$out = '<span style="color:#1F6D81; font-weight:bold;"><i>Object '. get_class($value) .'</i></span>';
			}else if(is_string($value)){
				$out = '<span style="color:#006228; font-weight:bold;">\''. htmlspecialchars($value) .'\'</span>';
			}else if(is_integer($value)){
				$out = '<span style="color:#E50088; font-weight:bold;">'. $value .'</span>';
			}else if(is_bool($value)){
				$out = '<span style="color:#C500E5; font-weight:bold;">'. strtoupper(($value?'true':'false')) .'</span>';
			}else if(is_null($value)){
				$out = '<span style="color:#1E90FF; font-weight:bold;">NULL</span>';
			}
			return $out;
		}
		
		static private function _build_args($args){
			$out = null;
			$tmp_array = array();
			foreach($args as $k => $value){
				$tmp_array[] = self::_dump_value($value);		
			}
			$out .= join(', ', $tmp_array);
			return $out;
		}
		
		static private function _tpl($content){
			$html = '<div class="backtrace">%1$s</div>';
			return sprintf($html, $content);
		}
		
		static public function getMemory(){
			
			$memory = memory_get_usage();
			
			if( 1024 >= ( $memory / 1024 ) ){
			
				$memory = ( $memory / 1024 );
				$memory = round( $memory, 2 ) . '&nbsp;kb';
				
			}else if( 1024 >= ( $memory / ( 1024 * 1024 ) ) ){
			
				$memory = ( $memory / ( 1024 * 1024 ) );
				$memory = round( $memory, 2 ) . '&nbsp;Mb';
				
			}
			
			return $memory;
			
		}
		
	}
