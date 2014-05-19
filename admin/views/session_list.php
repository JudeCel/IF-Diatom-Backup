<div class="table_heading">
  <h2>Sessions</h2>
</div>
<?php if($totalRows_retSession): ?>
  <div class="table_wrap">
    <table cellpadding="0" cellspacing="0" class="normal_table">
      <thead>
        <tr>
          <th>Session Name</th>
          <th>Facilitator</th>
          <th>Start</th>
          <th>End</th>
          <?php if($user_type < 2): ?>
            <th>Edit</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
      <?php while($row_retSession = mysql_fetch_assoc($retSession)):
        $brand_project = retrieve_brand_project($database_ifs, $ifs, $row_retSession['id']);

        $facilitator_name = '';
        if($brand_project && !is_string($brand_project)){
          $row_brand_project = mysql_fetch_assoc($brand_project);

          /* If the facilitator user id is found */
          if(isset($row_brand_project['user_id'])){
            $user_id = $row_brand_project['user_id']; //user id of facilitator
            $facilitator_result = retrieve_users($database_ifs, $ifs, true, true, $user_id);

            /* If facilitator is found */
            if($facilitator_result && !is_string($facilitator_result)){
              $row_facilitator = mysql_fetch_assoc($facilitator_result); //get row

              //If the appropriate fields are found
              if(isset($row_facilitator['name_first']) && isset($row_facilitator['name_last'])){
                $facilitator_name = $row_facilitator['name_first'] . ' ' . $row_facilitator['name_last'];
              }
            }
          }
        }?>
        <tr align="center">
          <td><?php echo $row_retSession['name']; ?></td>
          <td><?php echo ($facilitator_name ? $facilitator_name : 'N/A'); ?></td>
          <td><?php echo date('d-m-Y H:i', strtotime($row_retSession['start_time'])); ?></td>
          <td><?php echo date('d-m-Y H:i', strtotime($row_retSession['end_time'])); ?></td>
          <?php if($user_type < 2): ?>
            <td>
              <a title="Edit Session" class="editSession" href="newSession-edit.php?session_id=<?php echo $row_retSession['id']; ?>" alt=""><span class="ui-icon ui-icon-pencil"></span></a>
              <a title="Configure Session" href="session-emails.php?session_id=<?php echo $row_retSession['id']; ?>"><span class="ui-icon ui-icon-gear"></span></a>
              <?php if($user_type == -1): ?>
                <a title="Delete Session" href="SessionDelete.php?brand_project_id=<?php echo $brand_project_id; ?>&session_id=<?php echo $row_retSession['id']; ?>"><span class="ui-icon delete"></span></a>
              <?php endif; ?>
            </td>
          <?php endif; ?>      
        </tr>  
      <?php endwhile;

      if($totalRows_retSession > 0){
        mysql_data_seek($retSession, 0);                     
      }?>

      </tbody>          
    </table>
  </div>
<?php else: ?>
  <div class="text">
    <p>None added.</p>
  </div>
<?php endif;

if($user_type < 2): ?>
  <div id="add_item">
    <a class="buttons darker" href="newSession-insert.php?brand_project_id=<?php echo $brand_project_id; ?>"><span class="icon create"></span>Add Session</a>
  </div>
<?php endif; ?>