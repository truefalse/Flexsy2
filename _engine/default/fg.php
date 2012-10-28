<?php
	
    /**
        @package fenxGraph
        @author Ivan Gonarenko 
    */
    
    
    class fG{
        
        //data
        private $data_y = array();
        private $data_x = array();
        private $step_x = null;
        private $step_y = null;
        private $max_value = null;
        private $info_str = null;
        //image porperty
        private $image;
        private $image_x = 0;
        private $image_y = 0;
        private $margin_x = 50;
        private $margin_y = 35;
        //debug
        private $debug_str = array();
        
        function __construct($width=300,$height=200,$data,$info_str = null){
            $this->info_str = $info_str;
            if( intval($width) > 0 and intval($height) > 0 ){
                $this->image_x = (int) $width + $this->margin_x;
                $this->image_y = (int) $height + ($this->margin_y*2);
            }else{
                $this->image_x = 300;
                $this->image_y = 150;
            }
            if( ( $this->count_arr = (int) count( $data ) ) > 1 ){
                $this->data = $data;
                $this->data_x = array_keys( $data );
                $this->data_y = array_values( $data );
                $this->max_value = round((max($this->data_y)+((max($this->data_y)/100)*4)),1);
                $this->step_x = ($this->image_x-$this->margin_x)/(count($this->data_x)+1);
                $this->step_y = ($this->image_y-($this->margin_y*2))/count(array_unique($this->data_y));
            }
        }
                      
        private function create_image(){
            $this->image = imagecreatetruecolor($this->image_x,$this->image_y);
            if( function_exists('imageantialias') )
                imageantialias($this->image,true);
            return is_resource($this->image);
        }
        
        private function set_point($x,$y,$c=null){
            $x = is_null($x) ? 0 : (int) $x; 
            $y = is_null($y) ? 0 : (int) $y; 
            $bg = is_array($c[0]) ? $this->getcolor($c[0][0],$c[0][1],$c[0][2]) : $this->getcolor(99,112,137);
            $cc = is_array($c[1]) ? $this->getcolor($c[1][0],$c[1][1],$c[1][2]) : $this->getcolor(185,190,204);
            imagefilledellipse($this->image,$x,$y,6,6,$bg);
            imageellipse($this->image,$x,$y,6,6,$cc);
        }
        
        private function draw_graph_bg(){
            imagefilledrectangle(
                $this->image,
                $this->margin_x,
                $this->margin_y,
                $this->image_x-3,
                $this->image_y-$this->margin_y,
                $this->getcolor(66,73,91)    
            );         
            imagerectangle(
                $this->image,
                $this->margin_x,
                $this->margin_y,
                $this->image_x-3,
                $this->image_y-$this->margin_y,
                $this->getcolor(84,95,118)
            );   
        }
        
        private function draw_h_lines(){
            $size = count(array_unique($this->data_y));
            for( $i=0;$i<$size+1;$i++ ){
                imageline(
                    $this->image,
                    $this->margin_x-10,
                    $this->margin_y+($i*$this->step_y),
                    $this->image_x-3,
                    $this->margin_y+($i*$this->step_y),
                    $this->getcolor(84,95,118)
                );
            }
        }
        
        private function draw_v_lines(){
            $size = count($this->data_x)+1;
            for( $i=0;$i<$size;$i++ ){
                imageline(
                    $this->image,
                    $this->margin_x+($i*$this->step_x),
                    $this->margin_y,
                    $this->margin_x+($i*$this->step_x),
                    $this->image_y-$this->margin_y+10,
                    $this->getcolor(84,95,118)
                );
            }
        }
        
        private function draw_graph(){
            $px = ($this->image_y-($this->margin_y*2)) / $this->max_value;
            $size = count($this->data_x);
            for( $i=0;$i<$size;$i++ ){
                if( $size > $i+1 ){
                    imageline(
                        $this->image,
                        $this->margin_x+$this->step_x+($this->step_x*$i),
                        $this->image_y-$this->margin_y-($this->data_y[$i]*$px),
                        $this->margin_x+$this->step_x+($this->step_x*($i+1)),
                        $this->image_y-$this->margin_y-($this->data_y[$i+1]*$px),
                        $this->getcolor(255,50,0)
                    );
                }
                $this->set_point(
                    $this->margin_x+$this->step_x+($this->step_x*$i),
                    $this->image_y-$this->margin_y-($this->data_y[$i]*$px)
                );
            }
        }
        
        private function draw_values(){            
            $steps = (($this->image_y-($this->margin_y*2))/$this->step_y);
            $by_step = $this->max_value / $steps;        
            $val = $this->max_value;    
            for( $i=0;$i<($steps+1);$i++ ){
                if( $i > 0 ) $val = round($val - $by_step,1);
                if( $i+1 > $steps ) $val = 0;
                imagestring(
                    $this->image,
                    2,
                    $this->margin_x-12-(imagefontwidth(2)*strlen((string)$val)),
                    $this->margin_y+($this->step_y*$i)-(imagefontwidth(2)),
                    $val,
                    $this->getcolor(255,255,255)
                );   
            }
            $steps = (($this->image_x-$this->margin_y)/$this->step_x);
            for( $i=1;$i<=($steps+1);$i++ ){
                $val = $i == 1 ? 0 : $this->data_x[$i-2];
                $word_len = (imagefontwidth(2)*strlen((string)$val));
                if( $i % ( ( (int) ceil( $word_len / $this->step_x ) == 0 ) ? 1 : ceil( $word_len / $this->step_x )) == 0 ){
                    imagestring(
                        $this->image,
                        2,
                        $this->margin_x+($this->step_x*($i-1))-($word_len/2),
                        $this->image_y-$this->margin_y+12,
                        $val,
                        $this->getcolor(255,255,255)
                    );
                }
                
            }
        }
        
        private function getcolor($r,$g,$b){
            return imagecolorallocate($this->image,(int)$r,(int)$g,(int)$b);
        }
        
        private function draw_copyright(){
            $copyright = 'Ivan Gontarenko 2011 fG_1.0';
            imagestring(
                $this->image,
                2,
                $this->image_x - (imagefontwidth(2)*strlen($copyright)) - 3,
                2,
                $copyright,
                $this->getcolor(66,74,94)
            );
            if( isset( $this->info_str{1} ) ){
                $info_str = 'Graphic of:' . $this->info_str . ' (' . date('d-m-Y H-i-s') . ')';
                imagestring(
                    $this->image,
                    3,
                    $this->image_x - (imagefontwidth(3)*strlen($info_str)) - 3,
                    imagefontheight(3)+3,
                    $info_str,
                    $this->getcolor(200,200,200)
                );
            }            
        }
        
        private function render(){
            
            $this->create_image();
            
            imagefilledrectangle($this->image,$this->image_x,0,0,$this->image_y,$this->getcolor(43,48,60));
            
            $this->draw_graph_bg();
            $this->draw_h_lines();
            $this->draw_v_lines();
            $this->draw_graph();    
            $this->draw_values();
            $this->print_debug();
            $this->draw_copyright();
            
        }
        
        private function set_debug_string( $message = null ){
            $this->debug_str[] = $message;
        }
        
        private function print_debug(){
            if(!isset($this->debug_str[0])) return;            
            imagestring(
                $this->image,
                2,
                5,
                $this->image_y-imagefontheight(2)-2,
                'Debug:'.implode(' | ',$this->debug_str),
                $this->getcolor(200,200,200)
            );           
        }
        
        public function create_graph(){
            $this->render();
            header("content-type: image/png");
            imagepng($this->image);
            if(is_resource($this->image)) 
            	imagedestroy($this->image);
        }
        
    }
      
?>