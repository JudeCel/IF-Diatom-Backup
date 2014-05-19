<div class="table_heading">
  <h2>Registered Global Administrators</h2>
</div>
<?php if($totalRows_retClientUser): ?>
  <div class="table_wrap">  
    <table class="normal_table" cellspacing="0" borderspacing="0">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Mobile</th>
          <th>Phone</th>        
          <th class="edit">Edit</th>
        </tr>
      </thead>    
      <tbody>
        <?php while($row_retClientUser = mysql_fetch_assoc($retClientUser)): 
          $edit_link = '<a href="clientCompanyUsers-edit.php?user_id=' . $row_retClientUser['id'] . '" class="edit"><span class="ui-icon ui-icon-pencil"></span></a>';
        ?>
    			<tr>
            <td><?php echo $row_retClientUser['name_first'] . ' ' . $row_retClientUser['name_last'];?></td>
            <td><?php echo $row_retClientUser['email']; ?></td>
            <td><?php echo ($row_retClientUser['mobile'] ? $row_retClientUser['mobile'] : 'N/A'); ?></td>
            <td><?php echo ($row_retClientUser['phone'] ? $row_retClientUser['phone'] : 'N/A'); ?></td>            
            <td><?php echo $edit_link; ?></td>        
          </tr>
        <?php endwhile;

        if($totalRows_retClientUser > 0): 
          mysql_data_seek($retClientUser, 0);
        endif; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="text"><p>None registered.</p></div>
<?php endif; ?>
<div id="add_item">
  <a class="buttons darker" href="clientCompanyUsers-select.php?client_company_id=<?php echo $client_company_id; ?>"><span class="icon create"></span>Add Global Admin</a>
</div>