<!--
   
   Flexsy2 2012
   Ivan Gontarenko
   
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="fenx" />
	<title>Error 404 - Page not found</title>
	<style type="text/css">
		body{
			margin:0;
		}
		
		div.message{
			margin:30px auto 10px auto; 
			padding:5px; 
			width:90%;
			border: 1px solid #A7A7A7; 
			font-family: courier new;
			
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			
			-webkit-box-shadow: 0px 0px 17px rgba(50, 50, 49, 0.4);
			-moz-box-shadow:    0px 0px 17px rgba(50, 50, 49, 0.4);
			box-shadow:         0px 0px 17px rgba(50, 50, 49, 0.4);
		}
		
		div.message-box{
			border:1px solid #BBBBBB; 
			padding: 5px; 
			margin: 10px;
			font-size:14px;
			
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			
			text-shadow: .5px .5px 1px #000000;
			filter: dropshadow(color=#000000, offx=.5, offy=.5);

			-webkit-box-shadow: 0px 0px 9px rgba(50, 50, 50, 0.4);
			-moz-box-shadow:    0px 0px 9px rgba(50, 50, 50, 0.4);
			box-shadow:         0px 0px 9px rgba(50, 50, 50, 0.4);        0px 0px 8px rgba(50, 50, 50, 0.87);
					
			background: #ffffff; /* Old browsers */
			background: -moz-linear-gradient(top,  #ffffff 0%, #e5e5e5 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#e5e5e5)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* IE10+ */
			background: linear-gradient(top,  #ffffff 0%,#e5e5e5 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e5e5e5',GradientType=0 ); /* IE6-9 */
		}
		
		div.backtrace{
			padding: 5px; 
			margin: 10px;
			font-size:14px;
			border:1px solid #BBBBBB; 
			
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			
			-webkit-box-shadow: 0px 0px 9px rgba(50, 50, 50, 0.4);
			-moz-box-shadow:    0px 0px 9px rgba(50, 50, 50, 0.4);
			box-shadow:         0px 0px 9px rgba(50, 50, 50, 0.4);        0px 0px 8px rgba(50, 50, 50, 0.87);
		}
		
		
	</style>
</head>
<body>
	
	<div class="message">
		
		<?php 
		
				foreach( array('fatal_php', 'fatal' ) as $type){
					$error = $this->getErrors($type);
					$error = $error[0];
					
					if(empty($error)) continue;
					
					print '<div class="message-box">';
					print $error['__msg'];
					print '</div>';
					
					if( DEBUG ){
						print $error['__debug'];
					}
					
				}
				

		?>
		
	</div>

</body>
</html>