
<!--multi select js files. Somehow worked after removing the validate.js files. 
Maybe we can include those after these if needed.-->
<link rel="stylesheet" type="text/css" href="js/multiselect-new/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="js/multiselect-new/jquery.multiselect.filter.css" />

<script type="text/javascript" src="js/multiselect-new/jquery.multiselect.js"></script>
<script type="text/javascript" src="js/multiselect-new/jquery.multiselect.filter.js"></script>

<!-- Star Rating widget stuff here... -->
<script type="text/javascript" src="js/star/jquery.ui.stars.js"></script>
<link rel="stylesheet" type="text/css" href="js/star/css/jquery.ui.stars.css" />
   
   <!-- this is the file needed to enable edit in table... --> 
<script src="js/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" src="js/panel/ui.panel.min.js"></script>

<!-- Star Rating widget stuff here... -->
<script type="text/javascript" src="js/star/jquery.ui.stars.js"></script>
<link rel="stylesheet" type="text/css" href="js/star/css/jquery.ui.stars.css"/>
   
   <!-- this is the file needed to enable edit in table... --> 
<script src="js/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">
	var client_company_id = <?php echo $client_company_id; ?>,
			brand_project_id = <?php echo $brand_project_id; ?>,
			session_id = <?php echo $session_id; ?>,
			participants_json = <?php echo $participants_json; ?>;
</script>