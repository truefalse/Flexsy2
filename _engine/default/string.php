<?php
	
	class String{
		
		public function transliteration( $string = null, $safe = false ){
			 // Таблица русского алфавита:
			$trans_table_ru = array(
				'А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'Ё', 'ё', 
				'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 
				'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 
				'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ы', 'ы', 'Э', 'э', 'Ч', 'ч', 'Ш', 'ш', 
				'Щ', 'щ', 'Ю', 'ю', 'Я', 'я'
			);
			// Таблица латинского алфавита для адекватной замены букв (транслит):
			$trans_table_lat = array(
				'A', 'a', 'B', 'b', 'V', 'v', 'G', 'g', 'D', 'd', 'E', 'e', 'E', 'e', 
				'J', 'j', 'Z', 'z', 'I', 'i', 'Y', 'y', 'K', 'k', 'L', 'l', 'M', 'm', 
				'N', 'n', 'O', 'o', 'P', 'p', 'R', 'r', 'S', 's', 'T', 't', 'U', 'u', 
				'F', 'f', 'H', 'h', 'C', 'c', 'I', 'i', 'E', 'e',
				'Ch', 'ch', 'Sh', 'sh', 'Sh', 'sh', 'Yu', 'yu', 'Ya', 'ya'
			);
			$string = preg_replace('/\s+/','_',$string);
			if($string{0}){
				$string = str_replace($trans_table_ru,$trans_table_lat,$string);
			}
			if($safe == true){
				$string = preg_replace('/[^a-z\d\.-_]+/i','',$string);
			}
			return $string;
		}
		
	}
	
?>