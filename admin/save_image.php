<?php
	require_once("Connections/ifs.php");

	if(isset($_POST['image']) && isset($_POST['image_name'])){
		$image = $_POST['image'];
		$image_name = $_POST['image_name'];

		//set the header as an image
		header('Content-type: image/png');

		//Set to download
		header('Content-Disposition: attachment; filename="' . $image_name . '"');

		//Make image ready for download
		$encoded_image = str_replace(' ', '+', $image);

		//Display decoded image
		echo base64_decode($encoded_image);
	} else {
		header('Location: ' + $current_location);
	}

	mysql_close($ifs);