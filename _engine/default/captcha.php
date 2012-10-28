<?php
    
/**
 * @package		fCaptcha of FenxCMF
 * @version		1.0
 * @copyright	Copyright © 2010 - All rights reserved.
 * @license		GNU/GPL
 * @author		Ivan Gontarenko
 * @mail	    vania.gontarenko@gmail.com
 * @website		fenx.org
 */
 
 
    class Captcha{
        
        private $x=95;
        private $y=55;
        private $ttf;
        private $code;
        private $colors=array();
        
        function __construct( $ttf_file ){
            if( 100 < $width )  
                $this->x = $width;
            if( 50 < $height )
                $this->y = $height;
            if( ! file_exists( $ttf_file ) )
                return;
            $this->ttf = $ttf_file;
            $this->bgc = array(
                0xFDDED2,
                0xFEF8DE,
                0xE4FECD,
                0xCBFED6,
                0xD1FEFA,
                0xD0DDFF,
                0xFDC7FE,
                0xFFD2D7
            );
            $this->txtc = array(
                0xFD8002,
                0xDD6022,
                0x2EB881,
                0x18B848,
                0x2BC1D5,
                0x6A4EDE,
                0xC63AC6,
                0xC83747
            );
        }
        
        private function add_bg( & $image ){
            imagefilledrectangle( $image, 0, 0, $this->x, $this->y, $this->bgc[mt_rand(0,7)] );
        }
        
        private function get_code( $len=0 ){
            $array = array_merge(range('0','9'),range('a','z'));
            shuffle( $array );
            $code=null;
            for( $i=0; $i<$len; $i++ )
                $code .= $array[$i];      
            return $code;      
        }
        
        private function draw_text( & $image ){
            $this->code = $this->get_code(4);
            for( $i=0; $i<8; $i++ )
                imagettftext($image, 24, mt_rand(-5,5), 15*$i+15, 30, $this->txtc[mt_rand(0,7)], $this->ttf, $this->code{$i});
        }
        
        function create( $text = null ){
            
            if( ! $text{0} )
                $this->y = $this->y - 15;
            
            $image = imagecreatetruecolor( $this->x, $this->y );
            $this->add_bg( $image );
            $this->draw_text( $image );
            
            $lineColor = $this->txtc[mt_rand(0,7)];
            imagerectangle( $image, 0, 0, $this->x-1, $this->y-1, $lineColor );
            
            if( !! $text{0} ){
                imagefilledrectangle( $image, 0, 55, $this->x, $this->y, $this->bgc[mt_rand(0,7)] );
                imageline( $image, 0, 55, $this->x, 55, $lineColor );                            
                imagestring( $image, 2, ($this->x-(imagefontwidth(2)*strlen($text)))/2, 55, $text, $lineColor );
            }   
            session_start();                 
            $_SESSION['fCaptcha']['Code'] = md5($this->code);
            header( 'Content-type: image/png' );
            imagepng( $image );
            imagedestroy( $image );
        }
        
    }
 
?>