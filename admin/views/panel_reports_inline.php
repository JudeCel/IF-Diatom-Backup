<script type="text/javascript" src="js/panel/ui.panel.min.js"></script>
<script src="css/ui/jquery.ui.tabs.js"></script>

<!--jqplot includes-->
<link rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.min.css" />
<link rel="stylesheet" type="text/css" href="js/jqplot/examples.min.css" />
<link type="text/css" rel="stylesheet" href="js/jqplot/syntaxhighlighter/styles/shCoreDefault.min.css" />
<link type="text/css" rel="stylesheet" href="js/jqplot/syntaxhighlighter/styles/shThemejqPlot.min.css" />

<script type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot/syntaxhighlighter/scripts/shCore.min.js"></script>
<script type="text/javascript" src="js/jqplot/syntaxhighlighter/scripts/shBrushJScript.min.js"></script>
<script type="text/javascript" src="js/jqplot/syntaxhighlighter/scripts/shBrushXml.min.js"></script>

<!-- Additional jqplot plugins go here -->

<script type="text/javascript" src="js/jqplot/plugins/jqplot.pieRenderer.min.js"></script> 
<script type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.ohlcRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.bubbleRenderer.min.js"></script>

<script type="text/javascript">
	var brand_project_id = <?php echo $brand_project_id; ?>,
			male_participants = <?php echo $male_participants; ?>,
			female_participants = <?php echo $female_participants; ?>,
			
			brand_segment_json = <?php echo $brand_segment_json; ?>,
			segment_participants_json = <?php echo $segment_participants_json; ?>,

			state_json = <?php echo $state_json; ?>,
			state_participants_json = <?php echo $state_participants_json; ?>,
			suburb_json = <?php echo $suburb_json; ?>,
			suburb_participants_json = <?php echo $suburb_participants_json; ?>,
			country_json = <?php echo $country_json; ?>,
			country_participants_json = <?php echo $country_participants_json; ?>,

			occupations_json = <?php echo $occupations_json; ?>,
			occupation_participants_json = <?php echo $occupation_participants_json; ?>,
			ethnicity_json = <?php echo $ethnicity_json; ?>,
			ethnicity_participants_json = <?php echo $ethnicity_participants_json; ?>,
			bubble_array_json = <?php echo $bubble_array_json; ?>,
			
			optional01_json = <?php echo $optional01_json; ?>,
			optional01_participants_json = <?php echo $optional01_participants_json; ?>,
			optional02_json = <?php echo $optional02_json; ?>,
			optional02_participants_json = <?php echo $optional02_participants_json; ?>,
			optional03_json = <?php echo $optional03_json; ?>,
			optional03_participants_json = <?php echo $optional03_participants_json; ?>,
			optional04_json = <?php echo $optional04_json; ?>,
			optional04_participants_json = <?php echo $optional04_participants_json; ?>,
			optional05_json = <?php echo $optional05_json; ?>,
			optional05_participants_json = <?php echo $optional05_participants_json; ?>;
</script>

<script type="text/javascript" src="js/panel_analysis.js"></script>
<script type="text/javascript" src="js/panel_progress.js"></script>