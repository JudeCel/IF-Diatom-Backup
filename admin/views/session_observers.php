<section class="normal_content">
  <div class="table_heading">
    <div class="title">
      <h2>Invited Observers</h2>
    </div>
    <aside>
      <div class="inner">
        <?php echo ($max_number_of_observers - $totalRows_retSessionStaff); ?> Remaining Observers Allowed
      </div>
    </aside>
  </div>
  <?php if($totalRows_retSessionStaff): ?>
    <div class="table_wrap">
      <table cellpadding="0" cellspacing="0" class="normal_table">
        <thead>
            <tr>
              <th>Name</th>
              <th>Job Title</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Clear</td>
            </tr>
        </thead>
        <tbody>
          <?php while($row_retSessionStaff = mysql_fetch_assoc($retSessionStaff)): ?>
            <tr>
              <td><?php echo stripslashes(htmlspecialchars($row_retSessionStaff['name_first'])) . ' '. stripslashes(htmlspecialchars($row_retSessionStaff['name_last']));?></td>
              <td><?php echo stripslashes(htmlspecialchars($row_retSessionStaff['job_title'])); ?></td>
              <td><?php echo $row_retSessionStaff['email']; ?></td>
              <td><?php echo $row_retSessionStaff['mobile']; ?></td>
              <td>
                <?php if($user_type < 2): ?>
                  <a title="Clear Observer" href="session_staff-delete.php?session_staff_id=<?php echo $row_retSessionStaff['id']; ?>" class="deleteButton"/><span class="ui-icon delete"></span></a>
                <?php else:
                  echo 'N/A';
                endif; ?>               
              </td>
            </tr>  
          <?php endwhile;
       
          if($totalRows_retSessionStaff > 0){
            mysql_data_seek($retSessionStaff, 0);       
          }?>      
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="text"><p>No observers found.</p></div>
  <?php endif;?>
</section>
<section class="grid_content last">
  <div class="table_heading observers">
    <h2>Select Observers to Invite</h2>
  </div>

  <div class="items">No observers available.</div>

  <?php if($totalRows_retSessionStaff < $max_number_of_observers):?>
    <div class="actions">
      <a href="" class="buttons darker" id="getSelected"><span class="icon email"></span>Send Ticket</a>    
    </div>
  <?php endif; ?>
</section>
