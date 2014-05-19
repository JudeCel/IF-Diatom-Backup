<?php
	/* Create database results that are compatible with a foreach loop*/
	function prepare_foreach($results){
		$db_array = array();
		$num = 0;
		
		/* run through results and store keys and values in array */
		if($results && (mysql_num_rows($results) || !empty($results))){
			while($row = mysql_fetch_assoc($results)){
				foreach($row as $key=>$value){
					$db_array[$num][$key] = $value; //set value
				}

				$num++; //iterate array
			}
		}

		return $db_array;
	}
