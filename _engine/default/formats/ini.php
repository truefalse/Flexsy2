<?php
	
	
	class FormatIni extends Base{
		
		public function to_string($data){
			return INI::toString($data);
		}
		
	}