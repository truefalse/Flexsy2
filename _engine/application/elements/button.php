<?php

	class ButtonElement extends Element{

		public function fetch_element( $name, $value, $variants = '', $attrs = array() ){
			$attrs = $this->build_attrs($attrs);
			return '<input type="button" name="'. (string) $name .'" value="'. (string) $value .'" '. $attrs .'/>';
		}

	}