<?php

	class FormatJson extends Base{
		
		public function to_string($data){
			return json_encode($data);
		}
		
	}