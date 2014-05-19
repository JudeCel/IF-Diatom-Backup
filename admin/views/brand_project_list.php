<div class="table_heading">
  <h2>Brand Projects</h2>
</div>
<?php if($totalRows_retBPs): ?>
  <div class="table_wrap">
    <table cellspacing="0" class="normal_table">
      <thead>
        <tr>
          <th>BP Name</th>
          <th>Max Sessions</th>
          <th>Start</th>
          <th>End</th>
          <th>Replay Date</th>          
          <th class="edit">Edit</th>
          <?php if($user_type == -1): ?>
            <th>Delete</th>
          <?php endif; ?>                    	
        </tr>
      </thead>
      <tbody>
        <?php while($row_retBPs = mysql_fetch_assoc($retBPs)):?>
          <tr>
            <td><?php echo stripslashes(htmlspecialchars($row_retBPs['name'])); ?></td>
            <td><?php echo $row_retBPs['max_sessions']; ?></td>
            <td><?php echo date('d-m-Y',strtotime($row_retBPs['start_date'])); ?></td>
            <td><?php echo date('d-m-Y',strtotime($row_retBPs['end_date'])); ?></td>
            <td><?php echo date('d-m-Y',strtotime($row_retBPs['session_replay_date'])); ?></td>  
            <td>
              <a title="Edit Brand Project" href="newBrandProject-edit.php?brand_project_id=<?php echo $row_retBPs['id']; ?>" class="editBP"><span class="ui-icon ui-icon-pencil"></span></a>
              <a title="Configure Brand Project" href="newSession.php?brand_project_id=<?php echo $row_retBPs['id']; ?>"><span class="ui-icon ui-icon-gear"></span></a>
              <?php if($totalRows_retBPs < $row_retCompany['number_of_brands']):?>
               <a title="Clone Brand Project" class="cloneBrandProject" id="clone-<?php echo $row_retBPs['id']; ?>"  title="Copy Brand Project"><span class="ui-icon ui-icon-copy"></span></a>
              <?php endif; ?>
            </td>
            <?php if($user_type == -1): ?>
                <td><a href="BrandProjectDelete.php?brand_project_id=<?php echo $row_retBPs['id']; ?>&client_company_id=<?php echo $client_company_id;?>" title="Delete Brand Project"><span class="ui-icon delete"></span></a></td>
            <?php endif; ?>
          </tr>  
        <?php endwhile;
        if($totalRows_retBPs > 0): 
          mysql_data_seek($retBPs, 0);                     
        endif;?>                    
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="text">
    <p>None added.</p>
  </div>
<?php endif; ?>
<div id="add_item">
  <a class="buttons darker" href="newBrandProject-insert.php?client_company_id=<?php echo $client_company_id; ?>"><span class="icon create"></span>Add Brand Project</a>
</div>