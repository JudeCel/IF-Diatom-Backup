<?php
	/**
	* Set constants for image upload
	**/
	function set_image_constants($key = null, $filename = null, $brand_name = 'image', $bid = '', $random_key = null){
		$constants = array();

		if(!$filename){
			$filename = $brand_name . ($bid ? "-" . $bid : '') . ($random_key ? "-" . $random_key : '');
		}

		switch($key){
			default:
				
				$upload_dir = "upload/logo";
				$constants['upload_dir'] = $upload_dir; 		// The directory for the images to be saved in
				$constants['upload_path'] = $upload_dir."/";				// The path to where the image will be saved

				$large_image_prefix = "logo_";
				$constants['large_image_prefix'] = $large_image_prefix; 			// The prefix name to large image
				
				$thumb_image_prefix = "thumbnail_";
				$constants['thumb_image_prefix'] = $thumb_image_prefix;			// The prefix name to the thumb image

				$constants['large_image_name'] = $large_image_prefix . $filename;     // New name of the large image (append the timestamp to the filename)
				$constants['thumb_image_name'] = $thumb_image_prefix . $filename;     // New name of the thumbnail image (append the timestamp to the filename)
				
				$constants['max_file'] = "2"; 							// Maximum file size in MB
				$constants['max_width'] = "500";							// Max width allowed for the large image
				$constants['thumb_width'] = "300";						// Width of thumbnail image
				$constants['thumb_height'] = "100";						// Height of thumbnail image
				
				// Only one of these image types should be allowed for upload
				$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg", 'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
				$constants['allowed_image_types'] = $allowed_image_types;
				
				$allowed_image_ext = array_unique($allowed_image_types);
				$constants['allowed_image_ext'] = $allowed_image_ext; // do not change this
				
				$image_ext = "";	// initialise variable, do not change this.
				foreach ($allowed_image_ext as $mime_type => $ext) {
				    $image_ext.= strtoupper($ext)." ";
				}

				$constants['image_ext'] = $image_ext;

			break;
		}

		return $constants; //return constants
	}

	/**
	* Image upload - to be integrated later with the main system
	**/
	function image_upload($dir = null, $field = 'email_image', $width = null, $height = null, $crop = false){
		$img_path = FALSE;

		if((isset($_FILES[$field]['error']))&&($_FILES[$field]['error']==0)){ // if a file has been posted then upload it
			$root = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

			//Check if ifs test is visible
			$subdomain = '';
			if(preg_match('/^\/ifs-test/', $_SERVER['REQUEST_URI'])){
				$subdomain = '/ifs-test';
			}

			$dir_root = $_SERVER['DOCUMENT_ROOT'] . $subdomain . '/';			

			$dir_check = $dir_root . $dir;

			$userfile_size = $_FILES[$field]['size'];
			$max_file = 2;

			//check if the file size is above the allowed limit
			if ($userfile_size > ($max_file*1048576)) {
				return FALSE;
			}

			if(!is_dir($dir_check)){				
				$create_directory = mkdir($dir_check);
			}

			include('ampp.php'); //image upload class

			$myImage = new _image;
			
			// upload image
			$myImage->uploadTo = $dir; // SET UPLOAD FOLDER HERE
			$myImage->returnType = 'array';			

			$img = $myImage->upload($_FILES[$field]);
			$success = FALSE;
	
			if($img) {
				/* Set basic information */
				$init_path = ($subdomain ? str_replace('/', '', $subdomain) . '/' : '') . $img['path'];
				$img_path_val = $init_path . $img['image'];
				$image_size = getimagesize($img_path_val);				
				
			/* Resize and crop */
				if($width && !empty($image_size)){
					$resize_path = $init_path . 'resized/';
					
					$aspect = $image_size[0] / $image_size[1];
					$width_diff = $width / $image_size[0];
					$res_height = $width / $aspect;					
					
		
					/* Resize image */
					$myImage->source_file = $img_path_val;
					$myImage->newPath = $resize_path;
					$myImage->newWidth = $width;
					$myImage->newHeight = $res_height;
					$myImage->oversize = 'true';					
								
					$resize = $myImage->resize();
		
					if($resize){				
						
						/* Crop image */
						if($crop){
							$crop_path = $init_path . 'crop/';

							$myImage->source_file = $resize_path . $img['image'];
							$myImage->newPath = $crop_path;
							$myImage->oversize = 'true';
							$crop_process = $myImage->crop($width, $height, '0', '0');
	
							$img_path = $root . '/' . $crop_path . $crop_process['image'];

							/* Delete resized file */
							unlink($resize_path . $img['image']);							
						} else {
							$img_path = $root . '/' . $resize_path . $resize['image'];
						}
						
						/* Delete uploaded file */
						unlink($init_path . $img['image']);						
						
						
						/* Delete file if too small */
						if($image_size[0] < $width){
							unlink($resize_path . $img['image']);
							return FALSE;
						}
					}
				} else {
					$img_path = $root . '/' . $init_path . $img['image'];					
				}
			}
		}	

		return $img_path;
	}
