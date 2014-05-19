<script src="js/jscolor/jscolor.js" type="text/javascript"></script>
<form id="signupForm" enctype="multipart/form-data" method="post" action="<?php echo $form_action; ?>">
<fieldset>
<legend>Add Brand Project</legend>

<input id="number_of_brands" name="number_of_brands" type="hidden" value="<?php echo $totalRows_retBPs; ?>"/>
<input type="hidden" id="max_sessions" name="max_sessions"
       value="<?php echo $row_retCompany['max_sessions_brand']; ?>"/>
<input type="hidden" name="upload" value="1"/>

<div class="form_item">
    <label for="name">Brand Project Name <span class="required">*</span></label>
    <input id="name" name="name"
           value="<?php echo(isset($_POST['name']) ? htmlentities(mysql_real_escape_string($_POST['name'])) : ''); ?>"
        <?php echo(in_array('name', $fields) ? 'class="required"' : ''); ?> />
</div>

<div class="form_item">
    <label for="image">Upload Image for Dashboard & Email</label>
    <input type="file" name="image" size="30"/>
</div>

<!--Cheng-->
<?php if ($enable_chatroom_logo): //display upload pic ?>
    <div class="form_item">
        <label for="chatroom_image">Upload Image for Green & Chatroom</label>

        <div class="data">
            <input type="file" name="chatroom_image" size="30"/>

            <p>Size: 180 x 53 pixels</p>
        </div>
    </div>
<?php endif; ?>
<!--end-->

<div class="form_item">
    <label for="max_sessions">Max Sessions:</label>

    <div class="input_output"><?php echo $row_retCompany['max_sessions_brand']; ?></div>
</div>

<div class="form_item">
    <label for="start_date">Start Date: <span class="required">*</span></label>

    <div class="data">
        <input id="start_date" name="start_date"
               value="<?php echo(isset($_POST['start_date']) ? htmlentities(mysql_real_escape_string($_POST['start_date'])) : ''); ?>"
            <?php echo(in_array('start_date', $fields) ? 'class="required"' : ''); ?> />

        <p>Format: DD-MM-YYYY</p>
    </div>
</div>

<div class="form_item">
    <label for="end_date">End Date: <span class="required">*</span></label>

    <div class="data">
        <input id="end_date" name="end_date"
               value="<?php echo(isset($_POST['end_date']) ? htmlentities(mysql_real_escape_string($_POST['end_date'])) : ''); ?>"
            <?php echo(in_array('end_date', $fields) ? 'class="required"' : ''); ?> />

        <p>Format: DD-MM-YYYY</p>
    </div>
</div>

<!--Cheng Added to customized color setting for session-->
<fieldset>
    <div class="form_item"><img src="images/image001.jpg" style="max-width:100%;"/></div>
    <div class="form_item">
        <label>Browser Background:</label>

        <div class="data">
            <input id="browser_background" name="browser_background" class="color {hash:true}" value="#def1f8"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Background:</label>

        <div class="data">
            <input id="background" name="background" class="color {hash:true}" value="#ffffff"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Border:</label>

        <div class="data">
            <input id="border" name="border" class="color {hash:true}" value="#e51937" autocomplete="off"
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
                   value="#e1d8d8" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Whiteboard Border:</label>

        <div class="data">
            <input id="whiteboard_border" name="whiteboard_border" class="color {hash:true}" value="#a4918b"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Whiteboard Icon Background:</label>

        <div class="data">
            <input id="whiteboard_icon_background" name="whiteboard_icon_background" class="color {hash:true}"
                   value="#408ad2" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Whiteboard Icon Border:</label>

        <div class="data">
            <input id="whiteboard_icon_border" name="whiteboard_icon_border" class="color {hash:true}"
                   value="#a4918b" autocomplete="off"
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
            <input id="menu_background" name="menu_background" class="color {hash:true}" value="#679fd2"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Menu Border:</label>

        <div class="data">
            <input id="menu_border" name="menu_border" class="color {hash:true}" value="#043a6b"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Icon:</label>

        <div class="data">
            <input id="icon" name="icon" class="color {hash:true}" value="#e51937" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Text:</label>

        <div class="data">
            <input id="text" name="text" class="color {hash:true}" value="#e51937" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Label:</label>

        <div class="data">
            <input id="label" name="label" class="color {hash:true}" value="#679fd2" autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item"><img src="images/image004.jpg" style="max-width:100%;"/></div>
    <div class="form_item">
        <label>Button Background:</label>

        <div class="data">
            <input id="button_background" name="button_background" class="color {hash:true}" value="#a66500"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>

    <div class="form_item">
        <label>Button Border:</label>

        <div class="data">
            <input id="button_border" name="button_border" class="color {hash:true}" value="#ffc973"
                   autocomplete="off"
                   style="background-image: none; background-color: rgb(102, 255, 0); color: rgb(0, 0, 0);">

            <p>Format: #RRGGBB</p>
        </div>
    </div>
</fieldset>
<!--End-->


</fieldset>
<footer>
    <input class="buttons darker" id="btnSubmit" name="btnSubmit" type="submit" value="Save"/>
</footer>
</form>