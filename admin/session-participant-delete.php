<?php 
  require_once('Connections/ifs.php');     
  require_once('core.php');

//Check if javascript set an ajax value
  $ajax = false;
  if(isset($_GET['ajax'])){
    $ajax_str = $_GET['ajax'];

    //Check if ajax string is a stamptime
    if(is_numeric($ajax_str) && strlen($ajax_str) == 10){
      $ajax = true;
    } else {
      $_SESSION['notification'] = 'Please enable javascript';
    }
  }

  if(!$ajax || (!admin($database_ifs, $ifs) &&  $_SESSION['MM_UserTypeId'] > 1)){
    mysql_close($ifs);

    header('Location: index.php');
    die();
  }

  if(isset($_GET['participant_lists_id']))
  {
  	$participant_lists_id = strip_tags(mysql_real_escape_string($_GET['participant_lists_id']));	
  }
  else
  {
  	$participant_lists_id=-1;
  }


  //retrieve the sessions users
  mysql_select_db($database_ifs, $ifs);
  $query_retSessionParticipant = "
  SELECT 
    session_id,
		participant_colour_lookup_id
  FROM
    participant_lists
  WHERE
      participant_lists.id=$participant_lists_id
  ";
  $retSessionParticipant = mysql_query($query_retSessionParticipant, $ifs) or die(mysql_error());
	
	$totalRows_retSessionParticipant = 0;
	$row_retSessionParticipant = array();
	if($retSessionParticipant){
		$totalRows_retSessionParticipant = mysql_num_rows($retSessionParticipant);
		
		if($totalRows_retSessionParticipant){
			$row_retSessionParticipant = mysql_fetch_assoc($retSessionParticipant);	
		}
	}

  $updateGoTo = 'sessions.php';
	if(!empty($row_retSessionParticipant)){
		$updateGoTo = sprintf("session-participants-jqgrid.php?session_id=%d", $row_retSessionParticipant['session_id']);	
		
		mysql_select_db($database_ifs, $ifs);
		$update_colours_used = sprintf("SELECT colours_used FROM sessions WHERE id=%d", $row_retSessionParticipant['session_id']);
		$result = mysql_query($update_colours_used, $ifs);
		
		//Retrieve Colours
		$count_colours_used = 0;
		$colours_used_row = array();
		if($result){
			$count_colours_used = mysql_num_rows($result);
			
			if($count_colours_used){
				$colours_used_row = mysql_fetch_assoc($result);	
			}	
		}
		
		//Remove Participant Colour
		if(!empty($colours_used_row)){
			$colour = $row_retSessionParticipant['participant_colour_lookup_id'];
			$colours = json_decode($colours_used_row['colours_used'], true);
			
			$found=false;
            for ($i=0;$i<sizeof($colours);$i++)
            {
                if($colours[$i]==$colour)
                {
                    unset($colours[$i]);
                    break;
                }
            }
			
			$colours = array_values($colours);
			
			$update_colour = sprintf("UPDATE sessions SET colours_used='%s' WHERE id = %d", json_encode($colours), $row_retSessionParticipant['session_id']);
			mysql_select_db($database_ifs, $ifs) or die(mysql_error());
			$result = mysql_query($update_colour, $ifs)	or die(mysql_error());				
		}
		
		//delete session staff entry
		$insert3SQL = sprintf("DELETE from participant_lists WHERE id=%d", $participant_lists_id);
		mysql_select_db($database_ifs, $ifs);
		$Result3 = mysql_query($insert3SQL, $ifs) or die(mysql_error());
	} 
	
	mysql_close($ifs); 		

	header(sprintf("Location: %s", $updateGoTo));