<?php

//counts for brand_project's participants
function getBPCounts($database, $ifs, $brand_project_id, $column, $condition)
{
	//retrieve the brand project's details 
	mysql_select_db($database, $ifs);
	$query_retBPCounts = "
	SELECT 
		*
	FROM
		participants
		INNER JOIN users ON (participants.user_id = users.id)
		LEFT JOIN addresses ON (users.address_id = addresses.id)
	WHERE
		participants.brand_project_id=$brand_project_id AND $column='$condition'	
	";
	$retBPCounts = mysql_query($query_retBPCounts, $ifs) or die(mysql_error());
	$row_retBPCounts = mysql_fetch_assoc($retBPCounts);
	$totalRows_retBPCounts = mysql_num_rows($retBPCounts);
	return $totalRows_retBPCounts;		
}


//this function takes 3 parameters
//primary key of the table
//table name
//column name
//it returns the value of that column.
function getValue($database, $ifs, $primary, $table,$column)
{
	//retrieve the client_company details 
	mysql_select_db($database, $ifs);
	$query_retInfo = "
	SELECT 
		".$table.".".$column." AS Value
		
	FROM
		".$table."
	WHERE
		".$table.".id=$primary  

	";
	$retInfo = mysql_query($query_retInfo, $ifs) or die(mysql_error());
	$row_retInfo = mysql_fetch_assoc($retInfo);
	$totalRows_retInfo = mysql_num_rows($retInfo);
	
	$value= $row_retInfo['Value'];
	
	return $value;		
}
