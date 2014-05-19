<?php
require_once('Connections/ifs.php');     
require_once('core.php');

if(isset($_GET['CompanySuburbId'])){
 $CompanySuburbId = strip_tags(mysql_real_escape_string($_GET['CompanySuburbId']));
}

if(isset($_GET['State'])){
	
  $state = strip_tags(mysql_real_escape_string($_GET['State']));					   
  // Retreive all the suburbs belonging to a state
  mysql_select_db($database_ifs, $ifs);
  $query_retSuburb = "SELECT * from post_code_suburb_lookup where State = '".$state."'";
  
  $retSuburb  = mysql_query($query_retSuburb, $ifs) or die(mysql_error());
  // $row_retSuburb = mysql_fetch_assoc($retSuburb);
  $totalRows_retSuburb = mysql_num_rows($retSuburb);		
}
?>
<label>
                
              <select id= "suburbId" name="suburbId" type="text" >
                  <?php
                      while ($row_retSuburb = mysql_fetch_assoc($retSuburb)) 
					  { 
                      ?>
                         <option <?php if($CompanySuburbId == $row_retSuburb['SuburbId']) {echo  "selected=\"selected\""; }  ?>  value="<?php echo $row_retSuburb['SuburbId']?>"><?php echo $row_retSuburb['Suburb'].' - '.$row_retSuburb['PostCode']; ?></option>
                         <?php
                          } 
                            if($totalRows_retSuburb > 0) 
                            {
                                mysql_data_seek($retSuburb, 0);
                             
                            }
                          ?>
                          </select>
		</label>

<?php mysql_close($ifs); 
