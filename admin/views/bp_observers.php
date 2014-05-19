<div class="table_heading">
  <h2>Potential Observers</h2>
</div>
<div class="items">The content is optimised for Internet Explorer 8+, Mozilla Firefox and Google Chrome.</div>
<?php if(isset($user_type) && $user_type <= 1): ?>
	<div id="add_item">
	  <a class="buttons darker" href="clientCompanyUsers-insert.php?client_company_id=<?php echo $client_company_id; ?>&brand_project_id=<?php echo $brand_project_id; ?>&type_id=4"><span class="icon create"></span>Register New Observer</a>  
	</div>
<?php endif; ?>
