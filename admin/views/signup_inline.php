<script type="text/javascript" src="js/jquery.select.js" charset="utf-8"></script>
<script type="text/javascript" src="js/panel/ui.panel.min.js"></script>

<script type="text/javascript">
	var client_company_id = <?php echo $client_company_id; ?>,
			start_date = <?php echo $start_date; ?>,
			end_date = <?php echo $end_date; ?>,
			billing_address = <?php echo json_encode($row_retBillingAddress); ?>,
			billing_contact = <?php echo json_encode($row_retBillingContact); ?>;
</script>

<!-- for auto complete to work please include jquery-1.7.1.min.js file -->
<script src="css/ui/jquery.ui.autocomplete.js"></script>

<script type="text/javascript" src="js/panel_progress.js"></script>