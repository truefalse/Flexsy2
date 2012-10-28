jQuery(document).ready(function(){
	
	var conf 		= {
		id: 			'%{id}',
		swfPath: 		'%{swfPath}',
		maxFileSize: 	'%{maxFileSize}',
		uploadURL: 		'%{uploadURL}'
	};
	
	var uploader = new plupload.Uploader({
		runtimes 		: 'html5,flash',
		browse_button 	: 'addFiles-' + conf.id,
		container 		: 'container-' + conf.id,
		max_file_size 	: conf.maxFileSize,
		url 			: conf.uploadURL,
		flash_swf_url 	: conf.swfPath,
		filters : [
			{ title : "Image files", extensions : "jpg,jpeg,gif,png" }
		]
	});
	
	// Attach conf to uploader
	uploader.cfg = conf;
	
	// Add to global space
	window.%{uploaderVar} = uploader;
	
	jQuery( '#startUpload-' + conf.id ).click( function(e) {
		uploader.start();
		e.preventDefault();
	});

	uploader.init();

	uploader.bind( 'FilesAdded', function( u, files ) {		
		jQuery.each( files, function( i, file ) {
			jQuery( '#list-' + conf.id ).append(
				'<div class="fle-uploader-file" id="' + file.id + '"><div class="fle-uploader-filename">' +
				file.name + ' (' + plupload.formatSize( file.size ) + ') <b></b>' +
				'</div><div class="fle-uploader-progressbar"><div></div></div></div>'
			);
		});		
		u.refresh();
	});

	uploader.bind( 'UploadProgress', function( u, file ) {
		jQuery( '#' + file.id + ' div' ).css({ width: file.percent + '%' });
		jQuery('#' + file.id + " b").html( file.percent + "% Upload...");
	});

	uploader.bind('FileUploaded', function( u, file, res) {			
		jQuery('#' + file.id + " b").html("100% Done!" );
	});
	
		
});