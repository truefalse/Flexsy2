<?php

	class OutputHepler extends BaseStatic{
		
		static private $_vars = array(); 		
	
		static public function & get_tmpl_vars(){
			if( !is_array(self::$_vars['tmplvars']) ){
				self::$_vars['tmplvars'] = array();
			}
			return self::$_vars['tmplvars'];
		}
		
		static public function & get_header_vars(){
			if( !is_array(self::$_vars['header']) ){
				self::$_vars['header'] = array();
			}
			return self::$_vars['header'];
		}
		
		static public function getExt(){
			$useSmarty = Registry::getInstance()->get( 'config' )->get( 'useSmarty' );
			return $useSmarty ? 'tpl' : 'php';
		}
		
		static function getTemplateLayout( $template, $layout ){
			return self::getTemplateDir( $template ) . DS . $layout . '.' . self::getExt();
		}
		
		static function getTemplateDir( $template ){
			return TEMPLATES_PATH . DS . $template;
		}
		
		static function prepareData( array $data ){
		
			return array_merge(array(
				'header' 	=> self::_get_tmpl_header($data['header']),
				'module' 	=> self::_get_tmpl_module($data['data']),
				'message'	=> self::_get_tmpl_message()
			), self::_get_tmpl_vars( $data['data'] ));
			
		}
		
		static private function _get_tmpl_vars(array $data){
			$output = array();
			foreach( $data as $key => $value ){
				$output[$key] = implode( "\n", $value );
			}
			return $output;
		}
		
		static private function _get_tmpl_message(){
		
			$tmpl = '<div class="fle-mess-%1$s"><div class="fle-mess-title">%2$s</div>%3$s</div>'."\n";
			$out = null;
			$all_message = array();
			
			foreach( array( 'error', 'warning' ) as $type ){
				$all_message[$type] = Registry::getInstance()->get( 'error' )->getErrors( $type );
			}
			
			$all_message = array_merge( $all_message, Registry::getInstance()->get( 'message' )->fetch_messages() );
			
			foreach( $all_message as $type => $messages ){
			
				if(empty($messages)){
					continue;
				}
				
				$message_html = array();
				
				foreach( $messages as $message ){					
					if( empty( $message ) ){
						continue;
					}					
					$message_html[] = $message['__msg'];	
				}
				
				$messages = null;
				$out .= sprintf( $tmpl, $type, t( strtoupper( $type ) ), join( '<br />', $message_html ) );
			}
			
			return $out;
		}
		
		static private function _get_tmpl_module( & $module ){
			$data = implode( "\n", $module['module'] );
			unset($module['module']);
			return $data;
		}
		
		static private function _get_tmpl_header( & $header_data = array()){
			$header = array();
			$header[] = "\t" . '<title>'. join(' | ', $header_data['title']) .'</title>';			
			$header[] = "\t" . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';			
			$header[] = self::_get_tmpl_js($header_data['js']);
			$header[] = self::_get_tmpl_css($header_data['css']);
			unset($header_data);
			return join("\n", $header);
		}
		
		static private function _get_tmpl_css( & $css = array()){
			$out = array();
			if(!empty($css['files'])){
				foreach($css['files'] as $css_file){
					$out[] = "\t" . '<link rel="stylesheet" href="'. $css_file .'"/>';
				}
			}			
			if(!empty($css['declaration'])){
				$out[] = "\t" . '<style type="text/css">';
				$declaration = null;
				foreach($css['declaration'] as $value){
					$declaration .= $value."\n";
				}
				$out[] = $declaration;
				$out[] = "\t" . '</style>';
			}
			unset($css);
			return join("\n", $out);
		}
		
		static private function _get_tmpl_js( & $js = array()){
			$out = array();
			if(!empty($js['files'])){
				foreach($js['files'] as $js_file){
					$out[] = "\t" . '<script src="'. $js_file .'" type="text/javascript"></script>';
				}
			}			
			if(!empty($js['declaration'])){
				$out[] = "\t" . '<script type="text/javascript">';
				$declaration = null;
				foreach($js['declaration'] as $value){
					$declaration .= $value."\n";
				}
				$out[] = $declaration;
				$out[] = "\t" . '</script>';
			}
			unset($js);
			return join("\n", $out);
		}
		
	}
