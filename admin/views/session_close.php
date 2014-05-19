<div class="table_heading">
  <h2>Invited Participants</h2>
</div>
<?php if($totalRows_retSessionParticipant > 0):?>
  <div class="table_wrap">
    <table class="normal_table scroll"> 
      <thead>
        <tr>                                
          <th>Invited Participant</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Gender</th>
          <th>Rating</th>
          <th>Invite Again</th>
          <th>Comments</th>          
        </tr>
      </thead>
      <tbody>
        <?php while($row_retSessionParticipant = mysql_fetch_assoc($retSessionParticipant)): ?>
          <tr>
            <td><?php echo $row_retSessionParticipant['name_first'].' '.$row_retSessionParticipant['name_last'];?></td>
            <td><?php echo $row_retSessionParticipant['email']; ?></td>
            <td><?php echo $row_retSessionParticipant['mobile']; ?></td>
            <td><?php echo $row_retSessionParticipant['Gender']; ?></td>
            <td><?php
    					//retrieve rating types
    					mysql_select_db($database_ifs, $ifs);
    					$query_retparticipanRating = "SELECT * FROM participant_rating_lookup";
    					$retparticipanRating = mysql_query($query_retparticipanRating, $ifs) or die(mysql_error());

    					$totalRows_retparticipanRating = 0;

              if($retparticipanRating){
                $totalRows_retparticipanRating = mysql_num_rows($retparticipanRating);
              }?>

              <div id="pRating<?php echo $row_retSessionParticipant['id'];?>">
                <select name="participant_rating_id<?php echo $row_retSessionParticipant['id'];?>" id="participant_rating_id<?php echo $row_retSessionParticipant['id'];?>">
                  <?php while($row_retparticipanRating = mysql_fetch_assoc($retparticipanRating)): ?>
                    <option <?php if($row_retSessionParticipant['participant_rating_id'] == $row_retparticipanRating['id']) {echo  "selected=\"selected\""; }  ?> value="<?php echo $row_retparticipanRating['value']?>" ><?php echo $row_retparticipanRating['title']?></option>                            
                  <?php endwhile;
     
                  if($totalRows_retparticipanRating > 0){
                    mysql_data_seek($retparticipanRating, 0);               
                  }?>
                </select>
                <span id="caption"></span>
              </div>
            </td>
            <td><input <?php if (!(strcmp($row_retSessionParticipant['invite_again'],"Yes"))) {echo "checked=\"checked\"";} ?> name="invite_again<?php echo $row_retSessionParticipant['id'];?>" type="checkbox" id="invite_again<?php echo $row_retSessionParticipant['id'];?>" value="Yes"></td>
            <td><span id="comments<?php echo $row_retSessionParticipant['id'];?>" class="mouseover"><?php echo $row_retSessionParticipant['comments']; ?></span></td>
          </tr>  
    		<?php endwhile; ?>
    	</tbody> 
    </table>
  </div>
<?php else: ?>
  <div class="text"><p>No participants attended session.</p></div>
<?php endif;

if($totalRows_retSessionParticipant):?>
  <div id="add_item">
    <a class="buttons darker" href="selected-session-participants.php?session_id=<?php echo $session_id;?>&email_type_id=5"><span class="icon email"></span>Send Session Closed Email</a>
  </div>
<?php endif;