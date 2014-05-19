<?php
  require_once('Connections/ifs.php');     
  require_once('core.php');
  require_once('getCounts.php');

  //If the page was processed through ajax
  $ajax = FALSE;
  if(isset($_GET['ajax'])){
    $ajax = TRUE;
  }

  if(isset($_GET['client_company_id'])){
  	$client_company_id = strip_tags(mysql_real_escape_string($_GET['client_company_id']));
  } else {
  	$client_company_id = -1;
  }

  if(isset($_GET['brand_project_id'])){
  	$brand_project_id = strip_tags(mysql_real_escape_string($_GET['brand_project_id']));
  } else {
  	$brand_project_id = -1;
  }

  //retrieve the client company info
  mysql_select_db($database_ifs, $ifs);
  $query_retCompany = "
  SELECT 
    client_companies.name,
    client_companies.start_date,
    client_companies.end_date,
    client_companies.number_of_brands
  FROM
    client_companies
  WHERE
    client_companies.id=$client_company_id  
  ";
  $retCompany = mysql_query($query_retCompany, $ifs) or die(mysql_error());
  $totalRows_retCompany = 0;
  $row_retCompany = array();

  if($retCompany){
    $row_retCompany = mysql_fetch_assoc($retCompany);
    $totalRows_retCompany = mysql_num_rows($retCompany);
  }

  //retrieve the brand projects 
  mysql_select_db($database_ifs, $ifs);
  $query_retBPs = "
  SELECT 
    brand_projects.max_sessions,
    brand_projects.name,
    brand_projects.id,
    brand_projects.end_date,
    brand_projects.start_date
  FROM
    brand_projects
    INNER JOIN client_users ON (brand_projects.client_company_id = client_users.client_company_id)
  WHERE
     client_users.client_company_id=$client_company_id  
  GROUP BY
    brand_projects.id";

  $retBPs = mysql_query($query_retBPs, $ifs) or die(mysql_error());
  $totalRows_retBPs = 0;

  if($retBPs){
    $totalRows_retBPs = mysql_num_rows($retBPs);
  }

  //retrieve the bp info
  mysql_select_db($database_ifs, $ifs);
  $query_retBPInfo = "
  SELECT 
    brand_projects.max_sessions,
    brand_projects.name,
    brand_projects.id,
    brand_projects.logo_thumbnail_url,
    brand_projects.end_date,
    brand_projects.start_date,
    brand_projects.client_company_id
  FROM
    brand_projects
  WHERE
    brand_projects.id=$brand_project_id  
  ";
  $retBPInfo = mysql_query($query_retBPInfo, $ifs) or die(mysql_error());
  $totalRows_retBPInfo = 0;
  $row_retBPInfo = array();

  if($retBPInfo){
    $row_retBPInfo = mysql_fetch_assoc($retBPInfo);
    $totalRows_retBPInfo = mysql_num_rows($retBPInfo);
  }

  //retrieve the sessions
  mysql_select_db($database_ifs, $ifs);
  $query_retSession = "
  SELECT 
    brand_projects.name AS BPName,
    sessions.name,
    sessions.start_time,
    sessions.end_time,
    sessions.id,
    users.name_last,
    users.name_first
  FROM
    sessions
    INNER JOIN brand_projects ON (sessions.brand_project_id = brand_projects.id)
    INNER JOIN session_staff ON (sessions.id = session_staff.session_id)
    INNER JOIN users ON (session_staff.user_id = users.id)
  WHERE
     session_staff.type_id=2 AND brand_projects.id=$brand_project_id   
  ";

  $retSession = mysql_query($query_retSession, $ifs) or die(mysql_error());
  $totalRows_retSession = 0;

  if($retSession){
    $totalRows_retSession = mysql_num_rows($retSession);
  }
			
  if($totalRows_retCompany > 0 && $totalRows_retBPs > 0){
		$value = (($totalRows_retBPs/$row_retCompany['number_of_brands'])*100);
	} else {
		$value = 0;
  }
			
	if($totalRows_retBPInfo > 0 && $totalRows_retSession > 0){
		$bp_value = (($totalRows_retSession/$row_retBPInfo['max_sessions'])*100);
	} else {
		$bp_value = 0;
  }

  //Stop processing, if nothing was found
  if(!$value && !$bp_value && $ajax){
    return;
  }
?>		
			
			
            
<script>

$(document).ready(function(){
	//for the panel
	$('.progress_wrap').panel({
      'stackable':false
  });
	
	
	//for the progress bar for brand projects
	$( "#progressbar_bp").progressbar({
	 value: <?php echo $value;?>
	});
	
	
	//for the progress bar for sessions
	$( "#progressbar_session").progressbar({
	 value: <?php echo $bp_value;?>
	});	
});
</script>		


<script src="css/ui/jquery.ui.core.js"></script>
<script src="css/ui/jquery.ui.widget.js"></script>

<script src="css/ui/jquery.ui.progressbar.js"></script>


<!--add the info panel-->
<div class="inner">
  <div class="progress_wrap panel" id="panel3">
    <?php if($totalRows_retSession > 0 && $totalRows_retBPInfo > 0): ?>
      <div class="sessions">
        <div id="progressbar_session"></div>
        <div class="text">        
          <p><?php echo $totalRows_retSession . ' out of ' . $row_retBPInfo['max_sessions'] . ' Sessions used'; ?></p>          
        </div>
      </div>
    <?php endif; ?>
    <?php if($totalRows_retCompany > 0 && $totalRows_retBPs > 0): ?>
      <div class="brand_projects">
        <div id="progressbar_bp"></div>
        <div class="text">        
            <p><?php echo $totalRows_retBPs . ' out of ' . $row_retCompany['number_of_brands'] . ' Brand Projects used'; ?></p>          
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
  mysql_close($ifs);   