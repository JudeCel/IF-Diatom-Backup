<form name="signupForm" id="signupForm" method="post" action="<?php echo $form_action; ?>">
	<fieldset class="last">    
        <legend>Reorder Topics</legend>
  		<div class="input_output">
            <p>To reorder a topic, please drag its box to the desired position and press <em>Reorder</em>.</p>
        </div>
        <ul id="sortable">
			<?php
            while($row_retTopic = mysql_fetch_assoc($retTopic)): ?>
                <li>
                    <input type="hidden" name="order[]" value="<?php echo $row_retTopic['id']?>" />
                    <div class="form_item">
                        <div class="sortable"><?php echo $row_retTopic['name']; ?></div>
                    </div>
                </li>
            <?php endwhile; ?>
		</ul>
    </fieldset>

    <footer>
        <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Reorder" />
    </footer>  
 </form>