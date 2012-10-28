<?php
    
    define( 'IMG_WATERMARK_TILE', -1 );
    define( 'IMG_WATERMARK_RIGHTBOTTOM', 1 );
    define( 'IMG_WATERMARK_LEFTBOTTOM', 2 );
    define( 'IMG_WATERMARK_RIGHTTOP', 3 );
    define( 'IMG_WATERMARK_LEFTTOP', 4 );
    define( 'IMG_WATERMARK_MIDDLE', 5 );
    
    class Image{
        
        private $image,
                $watermark;
        
        public function load( $filename ){
            
            if( ! file_exists( $filename ) ){
                throw new Exception( 'File dosnt exists<br />['. $filename .']' );
            }
            
            $imageInfo = getimagesize( $filename );
            
            switch( $imageInfo[2] ){
                case IMAGETYPE_JPEG:
                    $this->image = imagecreatefromjpeg( $filename );
                    break;
                case IMAGETYPE_GIF:
                    $this->image = imagecreatefromgif( $filename );
                    break;
                case IMAGETYPE_PNG:
                    $this->image = imagecreatefrompng( $filename );
                    break;
                default:
                    throw new Exception( 'Unknown type<br />['. $imageInfo[2] .']' );
                    break;  
            }
            
            return $this;
            
        }
        
        public function loadWatermark( $filename ){
            
            if( ! file_exists( $filename ) ){
                throw new Exception( 'Watermark file dosnt exists<br />['. $filename .']' );
            }
            
            $watermarkInfo = getimagesize( $filename );
            
            if( $watermarkInfo[2] == IMAGETYPE_PNG ){               
                $this->watermark = imagecreatefrompng( $filename );
            }else{
                throw new Exception( 'Watermark must be just a PNG' );
            }
            
            return $this;
            
        }
        
        public function output( $imagetype = IMAGETYPE_JPEG ){
            
            switch( $imagetype ){
                case IMAGETYPE_JPEG:
                    header( 'Content-type: image/jpeg' );
                    imagejpeg( $this->image );
                    break;
                case IMAGETYPE_GIF:
                    header( 'Content-type: image/gif' );
                    imagegif( $this->image );
                    break;
                case IMAGETYPE_PNG:
                    header( 'Content-type: image/png' );
                    imagepng( $this->image );
                    break;
                default:
                    header( 'Content-type: image/jpeg' );
                    imagejpeg( $this->image );
                    break;  
            }
            
            return $this;
            
        }
        
        public function save( $filename, $imagetype = IMAGETYPE_JPEG, $quality = 50, $permission = 0777 ){
        
            switch( $imagetype ){
                case IMAGETYPE_JPEG:
                    imagejpeg( $this->image, $filename, $quality );
                    break;
                case IMAGETYPE_GIF:
                    imagegif( $this->image, $filename );
                    break;
                case IMAGETYPE_PNG:
                    imagepng( $this->image, $filename );
                    break;
                default:
                    throw new Exception( 'Unknown type<br />['. $imagetype .']' );
                    break;  
            }
            
            if( 0 < $permission ){
                @ chmod( $filename, $permission );
            }
            
            return $this;
            
        }
        
        public function saveGif( $filename, $permission = 0777 ){
            return $this->save( $filename, IMAGETYPE_GIF, -1, $permission );
        }
        
        public function savePng( $filename, $permission = 0777 ){
            return $this->save( $filename, IMAGETYPE_PNG, -1, $permission );
        }
        
        public function saveJpeg( $filename, $quality = 50, $permission = 0777 ){
            return $this->save( $filename, IMAGETYPE_GIF, $quality, $permission );
        }
        
        public function resize( $width = -1, $height = -1 ){
            
            if( 0 >= (int) $width || 0 >= (int) $height ){
                throw new Exception( 'Width or height is wrong<br />[Width:'. $width .',Height:'. $height .']' );
            }
            
            $blankImage = imagecreatetruecolor( $width, $height );          
            imagecopyresampled( $blankImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );            
            $this->image = $blankImage;
            
            return $this;
            
        }
        
        public function resizeToWidth( $width ){
        
            $ratio  = $width / $this->getWidth();
            $height = $this->getHeight() * $ratio;
            
            return $this->resize( $width, $height );
            
        }
        
        public function resizeToHeight( $height ){
        
            $ratio  = $height / $this->getHeight();
            $width  = $this->getWidth() * $ratio;
            
            return $this->resize( $width, $height );
            
        }
        
        public function scale( $scale = 50 ){
        
            $width  = $this->getWidth() * $scale / 100;
            $height = $this->getHeight() * $scale / 100;
            
            return $this->resize( $width, $height );
            
        }
        
        public function getWidth(){
            return imagesx( $this->image );
        }
        
        public function getHeight(){
            return imagesy( $this->image );
        }
        
        public function addWatermark( $position = IMG_WATERMARK_RIGHTBOTTOM ){
            
            $margin             = 10;
            $imageWidth         = $this->getWidth();
            $imageHeight        = $this->getHeight();
            $watermarkWidth     = imagesx( $this->watermark );
            $watermarkHeight    = imagesy( $this->watermark );
            
            switch( $position ){
            
                case IMG_WATERMARK_LEFTTOP:
                
                    $x = $margin;
                    $y = $margin;
                    
                    imagecopy( $this->image, $this->watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight );   
                    
                    break;
                    
                case IMG_WATERMARK_RIGHTTOP:    
                
                    $x = $imageWidth - $margin - $watermarkWidth;
                    $y = $margin;
                    
                    imagecopy( $this->image, $this->watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight );   
                    
                    break;                   
                    
                case IMG_WATERMARK_LEFTBOTTOM:      
                
                    $x = $margin;
                    $y = $imageHeight - $margin - $watermarkHeight;
                    
                    imagecopy( $this->image, $this->watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight );   
                    
                    break;        
                    
                case IMG_WATERMARK_RIGHTBOTTOM:     
                
                    $x = $imageWidth - $margin - $watermarkWidth;
                    $y = $imageHeight - $margin - $watermarkHeight;
                    
                    imagecopy( $this->image, $this->watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight );   
                    
                    break;                          
                    
                case IMG_WATERMARK_MIDDLE:
                
                    $x = $imageWidth - ( ( $imageWidth / 2 ) + ( $watermarkWidth / 2 ) );
                    $y = $imageHeight - ( ( $imageHeight / 2 ) + ( $watermarkHeight / 2 ) );
                    
                    imagecopy( $this->image, $this->watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight );   
                    
                    break;           
                    
                case IMG_WATERMARK_TILE:
                    
                    $inline     = ceil( $imageWidth / $watermarkWidth );
                    $lines      = ceil( $imageHeight / $watermarkHeight );
                    
                    for( $i = 0; $i < $lines; $i++ ){
                    
                        for(  $j = 0; $j < $inline; $j++  ){
                        
                            $x = $j * $watermarkWidth;
                            $y = $i * $watermarkHeight;
                            
                            imagecopy( $this->image, $this->watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight );
                            
                        }
                        
                    }
                                              
                    break;       
                    
            }
            
            return $this;
            
        }
        
        static public function getInstance(){
            
            static $instance;
            
            if( empty( $instance ) ){
                $instance = new Image;
            }
            
            return $instance;
            
        }
        
    }