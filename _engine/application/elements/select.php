<?php

	class SelectElement extends Element{

		public function fetch_element( $name, $value, $variants, $attrs = array() ){
			$attrs = $this->build_attrs($attrs);
			
			if( ! is_array( $variants ) ){
				return 'not Array';
			}
			
			$out = '<select name="'. $name .'" '. $attrs .'>';
			
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