<div class="table_heading">
  <h2>Topics</h2>
</div>
<?php if($totalRows_retTopic): ?>
<div class="table_wrap">
  <table cellpadding="0" cellspacing="0" class="normal_table">
    <thead>
      <tr>
        <th>Topic Name</th>
        <th>Active</th>
        <th>Open/Close</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row_retTopic = mysql_fetch_assoc($retTopic)): ?>
      <tr>
        <td><?php echo $row_retTopic['name']; ?></td>
        <td><input <?php if (!(strcmp($row_retSessionInfo['active_topic_id'],$row_retTopic['id']))) {echo "checked=\"checked\"";} ?> name="active_topic" id="active_topic" type="radio" value="<?php echo $row_retTopic['id']; ?>"></td>
        <td><input <?php if (!(strcmp($row_retTopic['topic_status_id'],"1"))) {echo "checked=\"checked\"";} ?> name="topic_status_id<?php echo $row_retTopic['id'];?>" type="checkbox" id="topic_status_id<?php echo $row_retTopic['id'];?>" value="1"></td>
        <td><a href="topic-delete.php?topic_id=<?php echo $row_retTopic['id']; ?>" title="Delete Topic" class="deleteButton"/><span class="ui-icon delete"></span></a>
        <div align="left" id="deleteText" style="display: none; padding: 5px">Are you sure you want to delete topic  <br /> </div></td>
      </tr>  
    <?php endwhile;

  	if($totalRows_retTopic > 0){
  		mysql_data_seek($retTopic, 0);	
  	}?>                       
    </tbody>
  </table>
</div>
<?php else: ?>
  <div class="text"><p>No topics found.</p></div>
<?php endif; ?>
<div id="add_item">
  <a class="buttons darker" href="newTopic-insert.php?session_id=<?php echo $session_id; ?>"><span class="icon create"></span>Add Topic</a>
  <?php if($totalRows_retTopic > 1): ?>
    <a class="buttons darker" href="topic-reorder.php?session_id=<?php echo $session_id; ?>"><span class="icon play"></span>Reorder Topics</a>
  <?php endif; ?>
</div>