<?php
	//Set the path of the page
	$root = $_SERVER['REQUEST_URI'];
	//Check if root includes ifs-test
	if(preg_match('/^\/ifs-test/', $root)){
		$root = str_replace('/ifs-test/', '', $root);
	} else {
		$root = str_replace('/', '', $root);
	}
	
	//Set the path of the page
	$path = $root;
	
	$num_menu_items = count($sub_navigation);
	$num = 1;

	if($sub_id):?>
		<div class="sub_navigation">
			<ul>
				<?php foreach($sub_navigation as $sub_item=>$url): 
					$class = '';

					//If this is the first item
					if($num == 1){
						$class .= 'first';
					}

					//if this is the last item
					if($num == $num_menu_items){
						$class .= ($class ? ' ' : '') . 'last';
					}

					//if this is the active menu item
					if($url == $path){
						$class .= ($class ? ' ' : '') . 'active';
					}

					$num++;
				?>
					<li<?php echo ($class ? ' class="' . $class . '"' : ''); ?>>
						<a href="<?php echo $url; ?>" class="<?php echo $sub_group; ?>_menu"><?php echo $sub_item; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div> 
	<?php endif;