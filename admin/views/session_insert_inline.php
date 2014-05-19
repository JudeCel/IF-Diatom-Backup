<script src="js/jquery.cookie.js"></script>
<script type="text/javascript">
	var number_of_sessions = <?php echo $max_sessions; ?>,
			min_date = <?php echo "'" . date('d-m-Y', $min_date) . "'"; ?>,
			max_date = <?php echo "'" . date('d-m-Y', $max_date) . "'"; ?>,
			no_notifications = true;
</script>