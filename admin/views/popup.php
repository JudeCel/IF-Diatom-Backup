<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gte IE 9]> <html class="ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title>Insiderfocus | <?php echo $page; ?></title>

  <!-- META TAGS -->
  <meta name="description" content="Insiderfocus">
 	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	
 	<!-- General CSS -->
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/generic.css" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/master.css" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/misc.css" />
	<link rel="stylesheet" type="text/css" media="print" href="css/print.css" />

	<!-- Plugin CSS -->
	<link rel="stylesheet" type="text/css" href="css/custom-theme-white/jquery-ui-1.8.20.custom.css" />
	<link rel="stylesheet" type="text/css" href="css/ui.jqgrid.css" />

	<link rel="stylesheet" href="sass/stylesheets/screen.css" media="screen" />
  <link rel="stylesheet" href="sass/stylesheets/print.css" media="print" />

	<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="boilerplate/js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>

  <!-- jQuery UI Plugins -->
  <script src="css/ui/jquery.ui.core.js"></script>
  <script src="css/ui/jquery.ui.widget.js"></script>
  <script src="css/ui/jquery.ui.datepicker.js"></script>
  <script src="css/ui/jquery-ui-timepicker-addon.js"></script>


	<script type="text/javascript" src="js/json2.js"></script>

	<?php if($validate): ?>
		<script src="js/jquery.validate.js" type="text/javascript"></script> 
	<?php endif; ?>

	<?php if($inline_scripting && is_string($inline_scripting)):
		include('views/' . $inline_scripting . '.php');
	endif; ?>


	<?php if($main_script): //If a page script is required ?>
		<script type="text/javascript" src="js/<?php echo $main_script; ?>.js"></script>
	<?php endif; ?>

	<script src="js/notification.js"></script>

	<script src="boilerplate/js/libs/modernizr-2.5.3.min.js"></script>
</head>
<body>
	<div class="wrap" id="popup">
    <header>
			<div class="title">
				<h1><?php echo $title; ?></h1>
			</div>
		</header><!--#header -->

    <div id="content" role="main">
    	<div class="inner">
				<?php if(isset($message) && $message && is_string($message)):?>
					<div class="notification">
						<div class="inner">
							<?php echo $message; ?>
						</div>
					</div>
				<?php endif;				

				if($other_content):?>
					<div class="content">
						<?php if(is_string($other_content)): ?>
							<div class="inner">
								<?php include('views/' . $other_content . '.php'); ?>
							</div>
						<?php endif; ?>									
					</div>
				<?php endif; ?>
			</div>
    </div>
  </div>

  <!-- JavaScript at the bottom for fast page loading -->
  <!-- scripts concatenated and minified via build script -->
  <script src="boilerplate/js/plugins.js"></script>   
</body>
</html>