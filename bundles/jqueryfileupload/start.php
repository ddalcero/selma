<?php
/*
 * jQueryFileUpload Bundle for Laravel
 * https://github.com/boparaiamrit/laravel-jqueryfileupload
 *
 * Plugin from
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

//Autoload UploadHander Class of jqueryfileupload
Autoloader::map(array(
    'UploadHandler' => path('bundle').DS.'jqueryfileupload'.DS.'UploadHandler'.EXT,
));

// Register the upload handler in the IoC container
Laravel\IoC::singleton('uploadhandler', function(){
	
	$option = array();
	foreach (Config::get('jqueryfileupload::fileupload') as $key => $value) {
		$option[$key] = $value;
	}

	return new UploadHandler($option);
});
