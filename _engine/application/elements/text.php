<?php

	class TextElement extends Element{

		public function fetch_element( $name, $value, $variants, $attrs = array() ){
		
			$attrs = $this->build_attrs($attrs);
			
			return '<input type="text" name="'. (string) $name .'" value="'. (string) $value .'" '. $attrs .'/>';
			
		}

	}