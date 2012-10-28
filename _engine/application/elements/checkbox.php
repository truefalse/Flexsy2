<?php

	class CheckboxElement extends Element{
		
		public function fetch_element( $name, $value, $datas, $attrs = array() ){
			
			if( ! is_array( $datas ) ){
				__CLASS__ . ' : no Array';
			}
			
			$out = '';
			
			foreach( $datas as $key => $label ){
				
				if( $key == $value || ( is_array( $value ) && in_array( $key, $value ) ) ){
					$attrs['checked'] = 'checked';
				}else{
					unset( $attrs['checked'] );
				}
				
				$attrs['id'] = 'chkBoxId-' . md5( $key );
					
				$attrsString = $this->build_attrs( $attrs );
				
				$out .= '<div>';
				$out .= '<input type="checkbox" name="'. (string) $name .'" value="'. (string) $key .'" '. $attrsString .'/>';
				$out .= '<label for="'. $id .'">'. $label .'</label>';
				$out .= '</div>';
				
			}
			
			return $out;
		}
		
	}