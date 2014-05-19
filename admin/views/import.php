<?php
	if($inline_scripting && is_string($inline_scripting)){
		include('views/' . $inline_scripting . '.php');
	}

	//hide all inactive form_items, except the first one ?>
	<script type="text/javascript" src="js/import.js"></script>

	<script src="js/jquery.cookie.js"></script>

	<?php if($main_script): //If a page script is required ?>
		<script type="text/javascript" src="js/<?php echo $main_script; ?>.js"></script>	
	<?php endif;

	if(isset($message) && $message && is_string($message)):?>
		<div class="notification">
			<div class="inner">
				<?php echo $message; ?>
			</div>
		</div>
	<?php endif;

	if($other_content && is_string($other_content)){
		include('views/' . $other_content . '.php');
	}