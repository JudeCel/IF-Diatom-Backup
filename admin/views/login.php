<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gte IE 9]> <html class="ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title>Insiderfocus | Login</title>

  <!-- META TAGS -->
  <meta name="description" content="Insiderfocus">
 	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/generic.css" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/master.css" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="css/misc.css" />

	<link rel="stylesheet" type="text/css" media="print" href="css/print.css" />

	<link rel="stylesheet" href="boilerplate/css/style.css" />
	<link rel="stylesheet" href="sass/stylesheets/screen.css" />

	<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="boilerplate/js/libs/jquery-1.7.1.min.js"><\/script>')</script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>	
	
	<script src="js/jquery.validate.js"></script>
	<script src="js/jquery.placeholder.js"></script>
	<script src="js/login.js"></script>

	<script src="boilerplate/js/libs/modernizr-2.5.3.min.js"></script>
</head>
<body>
	<div class="wrap" id="login">
		<div class="inner">
			<header>
				<a href="index.php" id="login_logo"><img src="images/logo_l.gif" alt="Insiderfocus" /></a>

				<?php if(isset($message) && $message):?>
					<div class="notification">
						<div class="inner">
							<div id="message">
								<?php echo $message; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</header>

			<div id="content" role="main">
				<form class="cmxform" method="POST" action="<?php echo $loginFormAction; ?>">
					<div class="form_item">
						<label for="username">Username</label>
						<input type="email" id="username" placeholder="Username" name="username" />
					</div>
					<div class="form_item">
						<label for="password">Password</label>
						<input id="password" placeholder="Password" name="password" type="password"/>
					</div>
				        
					<div class="buttons darker">
						<span class="icon play"></span>
						<input id="login_btn" name="login_btn" type="submit" value ="Log In" />
					</div>
				</form>
	    
				<div id="forgot_password">
					<p>Click <a href="forgot_password.php">here</a> if you forgot your password.</p>
				</div>
			</div><!--#content -->
		</div>
	</div><!--.wrap/#login -->

	<!-- JavaScript at the bottom for fast page loading -->
  <!-- scripts concatenated and minified via build script -->
  <script src="boilerplate/js/plugins.js"></script>
</body>
</html>