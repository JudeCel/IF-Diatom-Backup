<?php require_once('Connections/ifs.php');     require_once('core.php');?>
<?php
$return_arr = array();
$key = strip_tags(mysql_real_escape_string($_GET['key']));

//retrieve the company types 
mysql_select_db($database_ifs, $ifs);


if($key=='country_name')
{
    $fetch = mysql_query("SELECT DISTINCT ".$key." FROM country_lookup where ".$key." like '%". mysql_real_escape_string($_GET['term']) . "%'"); 
    
}   
else
{    
    $fetch = mysql_query("SELECT DISTINCT ".$key." FROM post_code_suburb_lookup where ".$key." like '%". mysql_real_escape_string($_GET['term']) . "%'"); 
}
    while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
        //$row_array['id'] = $row['SuburbId'];
        $row_array['value'] = $row[$key];

        array_push($return_arr,$row_array);
    }
echo json_encode($return_arr);

mysql_close($ifs);
