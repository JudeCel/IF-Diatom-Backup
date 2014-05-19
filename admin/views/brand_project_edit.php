<script src="js/jscolor/jscolor.js" type="text/javascript"></script>
<form name="signupForm" id="signupForm" enctype="multipart/form-data" method="POST"
      action="<?php echo $form_action; ?>">
<fieldset>
    <legend>Edit Brand Project</legend>

    <div class="form_item">
        <label for="name">Brand Project Name <span class="required">*</span></label>
        <input id="name" name="name"
               value="<?php echo(isset($_POST['name']) && $_POST['name'] != $row_retBPs['name'] ? htmlspecialchars(mysql_real_escape_string($_POST['name'])) : stripslashes(htmlspecialchars($row_retBPs['name']))); ?>"
            <?php echo(in_array('name', $fields) ? 'class="required"' : ''); ?> />
    </div>

    <div class="form_item">
        <label for="max_sessions">Max sessions</label>

        <div class="input_output"><?php echo $row_retBPs['max_sessions']; ?></div>
        <input type="hidden" id="max_sessions" name="max_sessions" value="<?php echo $row_retBPs['max_sessions']; ?>"/>
    </div>

    <div class="form_item">
        <label for="start_date">Start Date <span class="required">*</span></label>

        <div class="data">
            <input id="start_date" name="start_date"
                   value="<?php echo(isset($_POST['start_date']) && $_POST['start_date'] != $company_start_date ? htmlentities(mysql_real_escape_string($_POST['start_date'])) : $company_start_date); ?>"
                <?php echo(in_array('start_date', $fields) ? 'class="required"' : ''); ?> />

            <p>Format: DD-MM-YYYY</p>
        </div>
    </div>

    <div class="form_item">
        <label for="end_date">End Date <span class="required">*</span></label>

        <div class="data">
            <input id="end_date" name="end_date"
                   value="<?php echo(isset($_POST['end_date']) && $_POST['end_date'] != $company_end_date ? htmlentities(mysql_real_escape_string($_POST['end_date'])) : $company_end_date); ?>"
                <?php echo(in_array('end_date', $fields) ? 'class="required"' : ''); ?> />

            <p>Format: DD-MM-YYYY</p>
        </div>
    </div>
</fieldset>

<?php if ($logo_url): //display upload pic ?>
    <fieldset>
        <div class="input_output">
            <img src="<?php echo $logo_url ?>"
                 alt="<?php echo stripslashes(htmlspecialchars($row_retBPs['name'])); ?> Logo"/>
        </div>
        <div class="form_item">
            <input type="checkbox" id="clear_image" name="clear_image" value="1"/>
            <label for="clear_image" class="checkbox">Clear Brand Project logo</label>
        </div>
    </fieldset>
<?php endif; ?>

<fieldset><!-- <?php echo $enable_chatroom_logo ? '' : 'class="last"' ?>>-->
    <div class="form_item">
        <label for="image">Upload Image</label>
        <input type="file" name="image" size="30"/>
    </div>
</fieldset>

<!--Cheng Set area to upload logo for chatroom and green room-->
<?php if ($chatroom_logo_url && $enable_chatroom_logo): //display upload pic ?>
    <fieldset>
        <div class="input_output">
            <img src="<?php echo $chatroom_logo_url ?>"
                 alt="<?php echo stripslashes(htmlspecialchars($row_retBPs['name'])); ?> Chatroom Logo"/>
        </div>
        <div class="form_item">
            <input type="checkbox" id="clear_chatroom_image" name="clear_chatroom_image" value="1"/>
            <label for="clear_chatroom_image" class="checkbox">Clear Green & Chatroom logo</label>
        </div>
    </fieldset>
<?php endif; ?>

<?php if ($enable_chatroom_logo): ?>
    <fieldset>
        <div class="form_item">
            <label for="chatroom_image">Upload Image<br/> for Green &<br/> Chatroom</label>

            <div class="data">
                <input type="file" name="chatroom_image" size="30"/>

                <p>Size: 180 x 53 pixels</p>
            </div>
        </div>
    </fieldset>
<?php endif; ?>
<!--End-->

<!--Cheng Added to customized color setting for session-->
<fieldset>
    <div class="form_item"><img src="images/image001.jpg" style="max-width:100%;"/></div>
    <div class="form_item">
        <label>Browser Background:</label>

        <div class="data">
            <input id="browser_background" name="browser_background" class="color {hash:true}"
                   value="<?php echo $colour_browser_background; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Background:</label>

        <div class="data">
            <input id="background" name="background" class="color {hash:true}" value="<?php echo $colour_background; ?>"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Border:</label>

        <div class="data">
            <input id="border" name="border" class="color {hash:true}" value="<?php echo $colour_border; ?>"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>
</fieldset>

<fieldset>
    <div class="form_item"><img src="images/image002.jpg" style="max-width:100%;"/></div>
    <div class="form_item">
        <label>Whiteboard Background:</label>

        <div class="data">
            <input id="whiteboard_background" name="whiteboard_background" class="color {hash:true}"
                   value="<?php echo $colour_whiteboard_background; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Whiteboard Border:</label>

        <div class="data">
            <input id="whiteboard_border" name="whiteboard_border" class="color {hash:true}"
                   value="<?php echo $colour_whiteboard_border; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Whiteboard Icon Background:</label>

        <div class="data">
            <input id="whiteboard_icon_background" name="whiteboard_icon_background" class="color {hash:true}"
                   value="<?php echo $colour_whiteboard_icon_background; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Whiteboard Icon Border:</label>

        <div class="data">
            <input id="whiteboard_icon_border" name="whiteboard_icon_border" class="color {hash:true}"
                   value="<?php echo $colour_whiteboard_icon_border; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>
</fieldset>

<fieldset class="last">
    <div class="form_item"><img src="images/image003.jpg" style="max-width:100%;"/></div>
    <div class="form_item">
        <label>Menu Background:</label>

        <div class="data">
            <input id="menu_background" name="menu_background" class="color {hash:true}"
                   value="<?php echo $colour_menu_background; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Menu Border:</label>

        <div class="data">
            <input id="menu_border" name="menu_border" class="color {hash:true}"
                   value="<?php echo $colour_menu_border; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Icon:</label>

        <div class="data">
            <input id="icon" name="icon" class="color {hash:true}" value="<?php echo $colour_icon; ?>"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Text:</label>

        <div class="data">
            <input id="text" name="text" class="color {hash:true}" value="<?php echo $colour_text; ?>"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Label:</label>

        <div class="data">
            <input id="label" name="label" class="color {hash:true}" value="<?php echo $colour_label; ?>"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item"><img src="images/image004.jpg" style="max-width:100%;"/></div>
    <div class="form_item">
        <label>Button Background:</label>

        <div class="data">
            <input id="button_background" name="button_background" class="color {hash:true}"
                   value="<?php echo $colour_button_background; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Button Border:</label>

        <div class="data">
            <input id="button_border" name="button_border" class="color {hash:true}"
                   value="<?php echo $colour_button_border; ?>" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>
</fieldset>
<!--End-->

<footer>
    <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Save"/>
</footer>
</form>