<?php

	abstract class Element extends Base{

		protected function build_attrs( $attrs = array() ){
		
			$out = null;
			
			if( empty( $attrs ) ){
				return null;
			}
			
			foreach ( $attrs as $key => $value ) {
				$out .= $key . '="' . $value .'" ';
			}
			
			return $out;
		}

		abstract public function fetch_element( $name, $value, $variants, $attrs = array() );

	}