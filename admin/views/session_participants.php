<section class="normal_content">
  <div class="table_heading">
    <h2>Invited Participants</h2>
  </div>

  <?php if($totalRows_retSessionParticipant > 0): ?>
    <div class="table_wrap">
      <table class="normal_table">    
        <thead>       
          <tr>       
            <th>Invited Participant</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Gender</th>
            <th>Reply</th>
            <th>Clear</th>                           
          </tr>      
        </thead>
        <tbody>
          <?php while($row_retSessionParticipant = mysql_fetch_assoc($retSessionParticipant)): ?>
            <tr>                                             
              <td>
                <span class="idname" id="<?php echo $row_retSessionParticipant['participant_id']; ?>">
                <?php 
                  echo $row_retSessionParticipant['name_first'] . 
                       ' ' . $row_retSessionParticipant['name_last'];
                ?>
                </span>
              </td>
              <td><?php echo $row_retSessionParticipant['email']; ?></td>
              <td>
                <span id="mobile_edit<?php echo $row_retSessionParticipant['id'];?>" class="mouseover" style="display: inline">
                  <?php echo $row_retSessionParticipant['mobile']; ?>
                </span>
              </td>
              <td><?php echo $row_retSessionParticipant['Gender']; ?></td>
              <td>
                <?php 
                  if($row_retSessionParticipant['reply_name']){
                    echo $row_retSessionParticipant['reply_name']; 
                  } else {
                    echo "No reply";
                  } 
                ?>
              </td>                                           
              <td>
                <a title="Remove Participant" id="delete-<?php echo $row_retSessionParticipant['id'];?>" class="deleteButton">
                  <span class="ui-icon delete"></span>
                </a>
                <div id="deleteText" style="display: none;">
                  Are you sure you want to remove the  participant.<br />
                </div>
              </td>
            </tr>  
          <?php endwhile; 
            if($totalRows_retSessionParticipant > 0){
               mysql_data_seek($retSessionParticipant, 0);
            }
          ?>
        </tbody> 
      </table>
    </div>
  <?php else: ?>
    <div class="text"><p>None invited.</p></div>
  <?php endif;?>

  <div class="actions">
    <a href="" class="buttons darker" id="btnGeneric"><span class="icon email"></span>Send Generic Email</a>
    <a href="" id="btnSMS" class="buttons darker"><span class="icon phone"></span>SMS</a>
  </div>
</section>

<section class="grid_content last">
  <div class="table_heading participants">
    <h2>Select Participants to Invite</h2>
  </div>

  <div class="items">No participants available</div>

  <div class="actions">  
    <?php if($total_accepted_participants < 8):?>
        <a href="" class="buttons darker" id="getSelected"><span class="icon email"></span>Send Invitation</a>
    <?php endif; ?>
    <a href="" class="buttons darker" id="getSelectedGeneric"><span class="icon email"></span>Send Generic Email</a>
    <a href="" id="getSelectedSMS" class="buttons darker"><span class="icon phone"></span>SMS</a>
  </div>
</section>