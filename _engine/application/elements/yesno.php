<?php

	class YesnoElement extends Element{

		public function fetch_element( $name, $value, $variants = '', $attrs = array() ){
			$attrs = $this->build_attrs($attrs);
			
			$out = '<select name="'. $name .'" '. $attrs .'>';
			
			$variants = array(
				1 => t( 'YES' ),
				0 => t( 'NO' )
			);
			
			foreach( $variants as $key => $text ){
				if( $key == $value ){
					$selected = ' selected="selected" ';
				}else{
					$selected = null;
				}
				$out .= '<option'. $selected .' value="'. $key .'">'. $text .'</option>';
			}
			
			$out .= '</select>';
			
			return $out;
		}

	}