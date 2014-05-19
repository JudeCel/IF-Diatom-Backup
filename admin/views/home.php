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
	<link rel="stylesheet" type="text/css" href="js/jqgrid/plugins/ui.multiselect.css" />

	<link rel="stylesheet" href="sass/stylesheets/screen.css" />

	<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="boilerplate/js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>

  <!-- jQuery UI Plugins -->
  <script src="css/ui/jquery.ui.core.js"></script>
  <script src="css/ui/jquery.ui.widget.js"></script>
  <script src="css/ui/jquery.ui.datepicker.js"></script>
  <script src="css/ui/jquery-ui-timepicker-addon.js"></script>

	<!-- Plugins -->
	<script type="text/javascript" src="js/jqgrid/plugins/ui.multiselect.js"></script>

	<?php if($grid): ?>
		<script type="text/javascript" src="js/jqgrid/i18n/grid.locale-en.js"></script>
		<script type="text/javascript" src="js/jqgrid/jquery.jqGrid.min.js"></script>
	<?php endif; ?>	

	<script type="text/javascript" src="js/json2.js"></script>

	<?php if($validate): ?>
		<script src="js/jquery.validate.js" type="text/javascript"></script> 
	<?php endif; ?>
	 
	<script type="text/javascript">
		var user_type = <?php echo (isset($_SESSION['MM_UserTypeId']) ? $_SESSION['MM_UserTypeId'] : 0); ?>;
	</script>

	<script type="text/javascript" src="js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

	<?php if($inline_scripting && is_string($inline_scripting)):
		include('views/' . $inline_scripting . '.php');
	endif; ?>

	<?php if($main_script): //If a page script is required ?>
		<script type="text/javascript" src="js/<?php echo $main_script; ?>.js"></script>		
	<?php endif;

	if($grid):?>
		<script type="text/javascript" src="js/grid.js"></script>
	<?php endif; ?>

	<script src="js/notification.js"></script>

	<script src="boilerplate/js/libs/modernizr-2.5.3.min.js"></script>
</head>
<body>
	<div class="wrap" id="home>">
    <header>
			<div id="logo">
				<a href="index.php"><img src="<?php echo (isset($brand_project_logo_url) ? $brand_project_logo_url : $_SESSION['session_logo']); ?>" alt="logo" /></a>
			</div>

			<nav>
				<div class="inner">
					<?php menu($database_ifs, $ifs); ?>
				</div>
			</nav>

		</header><!--#header -->

    <div id="content" role="main">
    	<div class="<?php echo $other_content; ?> inner<?php echo ' ' . ($sub_group ? $sub_group : strtolower(str_replace(' ', '_', $title))); ?>">
	    	<section id="page_info">
		    	<div class="title">
						<h1><?php echo $title; ?></h1>
					</div>
					<div id="profile">
						<div class="inner">
							<div id="user">
								<p>Welcome <em><?php echo $_SESSION['MM_FirstName']; ?></em></p>
							</div>

							<div id="actions">
								<div class="inner">
									<a href="help.php<?php echo (isset($page_help) && $page_help ? '?page=' . $page_help : ''); ?>" class="buttons" target="_blank" id="help_btn">
										<span class="icon question">Help</span>
									</a>
									<a href="profile.php" class="buttons">
										<span class="person icon">Your Profile</span>
									</a>									
									<a href="logout.php" id="logoutlink" class="buttons last">
										<span class="x icon">Log Out</span>
									</a>
								</div>
							</div>							
						</div>
					</div>
				</section>

				<?php if(isset($message) && $message && is_string($message)):?>
					<div class="notification">
						<div class="inner">
							<?php echo $message; ?>
						</div>
					</div>
				<?php endif;

				if(($sub_navigation && $sub_group) || isset($subtitle_found)):?>
					<div class="section_information<?php echo ($sub_group ? ' ' . $sub_group : ''); ?>">
						<?php 
							if(isset($subtitle_found) && (isset($client_company_name) || isset($brand_project_name))):?>
								<div class="page_subtitle">
									<?php
										$subtitle = '';										

										if(isset($client_company_name)){
											$subtitle .= $client_company_name;
										}

										if(isset($brand_project_name) && !isset($_GET['brand_project_id'])){
											/* Check if a subtitle is set */
											$border = false;
											if($subtitle){
												$border = true;
											}

											$subtitle .= ($border ? ' <span class="border">' : '') . stripslashes($brand_project_name) . ($border ? '</span>' : '');
										}

										echo stripslashes($subtitle); //output subtitle
									?>
								</div>
							<?php endif;

							if($sub_navigation && $sub_group){
								include('views/sub_navigation.php'); 
							}
						?>
					</div>					
				<?php endif;

				if($other_content):?>
					<div class="content">
						<?php if(is_string($other_content)): ?>
							<div class="inner">
								<?php include('views/' . $other_content . '.php'); ?>
							</div>
						<?php endif;
						if(isset($footer) && $footer && is_string($footer)):?>
							<footer>
								<?php include('views/' . $footer . '.php'); ?>
							</footer>
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