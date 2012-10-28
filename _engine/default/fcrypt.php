<?php


	class fCrypt{

       static private $symbFrom = '1234567890';
       static private $symbTo = '0_.1234567';

       function setMask( $mask ){
			$mask = (string) $mask;
			if( $mask{9} and ! $mask{10} ){
				self::$symbTo = $mask;
			}				
       }

       static function encrypt( $str, $key ){
	   
            $keyArr = str_split(sha1($key));
            $strArr = str_split($str);
            $keyCount = 0;
			
            for( $i = 0, $c = strlen($str); $i < $c; $i++ ){
			
                $strNums 	= ord( $strArr[$i] );
                $keyNums 	= ord( $keyArr[$keyCount] );
                $sum 		= $strNums+$keyNums;
				
                $allSum 	.= ( 2 < strlen( $sum ) ) ? $sum : '0' . $sum;
                $keyCount++;
				
                $keyCount = (count($keyArr) == $keyCount) ? 0 : $keyCount;
				
            }

            return strtr( $allSum, self::$symbFrom, self::$symbTo );
        }

       static function decrypt( $str, $key ){
	   
            $keyArr = str_split( sha1( $key ) );
            $strArr = str_split( strtr( $str, self::$symbTo, self::$symbFrom ), 3 );
            $keyCount = 0;
			
            for( $i = 0, $c = count($strArr); $i < $c; $i++ ){
                $strArrZeroCheck 	= str_split( $strArr[$i] );
                $strArrNext 		= ($strArrZeroCheck['0'] == 0) ? $strArrZeroCheck['1'].$strArrZeroCheck['2'] : $strArr[$i];
                $strRes 			.= chr( $strArrNext - ord( $keyArr[$keyCount] ) );
                $keyCount++;
                $keyCount 			= ( count( $keyArr ) == $keyCount ) ? 0 : $keyCount;
            }
			
            return $strRes;
        }
		
	}