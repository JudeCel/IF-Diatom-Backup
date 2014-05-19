<?php
/* If view is not loaded by correct file */
if (!isset($details)) {
    require_once('../Connections/ifs.php');
}

/* Check if logged in */
if ((!isset($_GET['session_id']) || !isset($_GET['email_type_id'])) && !isset($_GET['admin_email_type'])) {
    return FALSE;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo(isset($row_retSessionEmail['subject']) ? stripslashes($row_retSessionEmail['subject']) : 'Subject'); ?></title>
</head>
<body
    style="margin: 0; background-color:<?php echo(isset($row_retSessionEmail['colour_browser_background']) ? $row_retSessionEmail['colour_browser_background'] : '#DDF2F9'); ?> ;">
<div id="email" bgcolor="#DDF2F9"
     style="width: 100%; background-color: #<?php echo(isset($row_retSessionEmail['colour_browser_background']) ? $row_retSessionEmail['colour_browser_background'] : '#DDF2F9'); ?> ;">
<?php if (isset($preview) && $preview): ?>
    <div class="notification">
        <style type="text/css">
            div.notification {
                display: block;
                padding: 10px;
                color: white;
                width: 572px;
                margin: 20px auto;

                border-radius: 6px;
                -webkit-border-radius: 6px;
                -moz-border-radius: 6px;
                -khtml-border-radius: 6px;

                -webkit-box-shadow: 4px 3px 0px 0px rgba(94, 74, 62, 0.15);
                box-shadow: 4px 3px 0px 0px rgba(94, 74, 62, 0.15);
            }
        </style>
        <?php
        /* Display notification */
        $message = 'This is a preview of your updates';
        echo $message;
        ?>
    </div>
<?php endif; ?><!--swap $root to $ADMIN_URL as path change-->
<img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="580" height="40" alt=""/>
<table width="572" cellspacing="0" cellpadding="0" align="center"
       style="border: 4px solid <?php echo(isset($row_retSessionEmail['colour_border']) ? $row_retSessionEmail['colour_border'] : '#E21F3C'); ?> ; border-bottom-width: 0;"
       bgcolor="white">
<tr>
    <td width="29">
        <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
    </td>
    <td width="514" colspan="4" align="center">
        <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="514" height="7" alt=""
             style="width: 514px;"/>
        <img src="<?php echo $thumbnail_path; ?>" alt="InsiderFocus Logo" style="border: 0 none;"/><!--removed for different logo width="231"-->
    </td>
    <td width="29">
        <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
    </td>
</tr>
<?php if (isset($row_retSessionEmail['email_image']) && $row_retSessionEmail['email_image']): ?>
    <tr>
        <td width="29">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
        <td width="514" colspan="4">
            <img src="<?php echo $row_retSessionEmail['email_image']; ?>" alt="Main Image" width="512"
                 style="border: 1px solid #DFDBD8"/>
        </td>
        <td width="29">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
    </tr>
<?php endif; ?>
<tr>
    <td width="29">
        <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
    </td>
    <td width="514" colspan="4">
        <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="514" height="20" alt=""/>
        <?php if (isset($row_retSessionEmail['greeting'])): ?>
            <h1 style="font-weight: normal; margin: 0; width: 514px;" width="514">
                <font face="arial" size="5"
                      color="<?php echo(isset($row_retSessionEmail['colour_text']) ? $row_retSessionEmail['colour_text'] : '#E51937'); ?>">
                    <?php echo stripslashes($row_retSessionEmail['greeting']); ?>
                </font>
            </h1>
        <?php endif; ?>
        <?php if (isset($row_retSessionEmail['email_message_top']) && $row_retSessionEmail['email_message_top']): ?>
            <div class="content" width="514" style="width: 514px;">
                <p style="line-height: 1.2em; margin-bottom: 0;"><font face="arial" size="2" color="#5E4A3E">
                        <?php echo stripslashes(nl2br($row_retSessionEmail['email_message_top'])); ?>
                    </font></p>
            </div>
        <?php endif; ?>
    </td>
    <td width="29">
        <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
    </td>
</tr>
<?php if ((isset($details) && $details) || (isset($row_retSessionEmail['email_video']) && $row_retSessionEmail['email_video'])): ?>
    <tr>
        <td width="29">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
        <td colspan="4">
            <div id="details" style="width: 514px;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="514" height="18" alt=""/>
                <table width="514" cellspacing="0" cellpadding="0"
                       style="border: 1px solid DFDBD8; background-color: #EFEDEC;" bgcolor="#EFEDEC">
                    <tr>
                        <td colspan="4">
                            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="514" height="20"
                                 alt=""/>
                        </td>
                    </tr>
                    <tr>
                        <td width="15">
                            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="15" alt=""/>
                        </td>
                        <?php if ($details):
                            $td_width = 486;
                            $td_col = 2;
                            if (isset($row_retSessionEmail['email_video']) && $row_retSessionEmail['email_video']) {
                                $td_width = 313;
                                $td_col = 1;
                            }?>

                            <td width="<?php echo $td_width; ?>" colspan="<?php echo $td_col; ?>"><!-- 315 -->
                                <div class="content">
                                    <?php foreach ($details as $key => $detail): ?>
                                        <div style="border-bottom: 1px dotted #F4A8B3;">
                                            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png"
                                                 width="<?php echo $td_width; ?>" height="7" alt=""
                                                 style="width: <?php echo $td_width . 'px'; ?>;"/>

                                            <p style="margin: 0;">
                                                <font face="arial"
                                                      size="2"><?php echo(isset($row_retSessionEmail['detail_' . ($key + 1)]) ? stripslashes(nl2br($row_retSessionEmail['detail_' . ($key + 1)])) : nl2br($detail)) ?></font>
                                            </p>
                                            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png"
                                                 width="<?php echo $td_width; ?>" height="7" alt=""
                                                 style="width: <?php echo $td_width . 'px'; ?>;"/>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        <?php endif;
                        if (isset($row_retSessionEmail['email_video']) && $row_retSessionEmail['email_video']): ?>
                            <td width="<?php echo($details ? 171 : 484); ?>"
                                align="<?php echo($details ? 'right' : 'center'); ?>">
                                <a href="<?php echo $row_retSessionEmail['email_video']; ?>">
                                    <img src="<?php echo $ADMIN_URL; ?>images/new_layout/video.jpg" width="146"
                                         alt="Video Thumb" style="border: 0 none;"/>
                                </a>
                            </td>
                        <?php endif; ?>
                        <td width="15">
                            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="15" alt=""/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="514" height="15"
                                 alt=""/>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td width="29">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
    </tr>
<?php else: ?>
    <tr>
        <td width="572" colspan="6">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="572" height="20" alt=""/>
        </td>
    </tr>
<?php endif; ?>
<?php if (isset($email_buttons) && !empty($email_buttons)): ?>
    <tr>
        <td colspan="6">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="572" height="12" alt=""/>
        </td>
    </tr>
    <tr>
        <td width="29">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
        <?php
        $but_num = 3 - count($email_buttons);

        if ($but_num):
            $but_num_equal = $but_num / 2;

            $smaller = FALSE;
            if ($but_num_equal <= 1) {
                $but_num_equal = 2;
                $smaller = TRUE;
            }
            for ($i = 1; $i <= ($but_num_equal - 1); $i++):?>

                <td width="<?php echo($smaller ? 85 : 171); ?>">
                    <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png"
                         width="<?php echo($smaller ? 85 : 171); ?>" height="12" alt=""/>
                </td>
            <?php endfor;
        endif;

        $num = 0;
        foreach ($email_buttons as $button): ?>
            <td align="center" width="171" colspan="<?php echo(!$num && !$but_num ? 2 : 1); ?>">
                <a href="<?php echo $button->href; ?>">
                    <img src="<?php echo substr($ADMIN_URL, 0, -1) . $button->src; ?>"
                         alt="<?php echo $button->alt; ?>" title="<?php echo $button->title; ?>"
                         style="border: 0 none;"/>
                </a>
            </td>
            <?php
            $num++;
        endforeach;

        if ($but_num):
            for ($i = 1; $i <= ($but_num_equal - 1); $i++):?>

                <td width="<?php echo($smaller ? 85 : 171) ?>">
                    <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png"
                         width="<?php echo($smaller ? 85 : 171) ?>" height="12" alt=""/>
                </td>

            <?php endfor;
        endif;
        ?>
        <td width="29">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="572" height="12" alt=""/>
        </td>
    </tr>
<?php endif; ?>
</table>
<table width="596" cellspacing="0" cellpadding="0" bgcolor="#E5E2E0"
       style="background-color: #E5E2E0; border: 2px solid #DFDBD8;" align="center">
    <?php if ((isset($facilitator_firstname) && $facilitator_firstname) || (isset($row_retSessionEmail['email_message_bottom']) && $row_retSessionEmail['email_message_bottom'])): ?>
        <tr>
            <td colspan="7">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="596" height="20" alt=""/>
            </td>
        </tr>
    <?php endif; ?>
    <?php if (isset($row_retSessionEmail['email_message_bottom']) && $row_retSessionEmail['email_message_bottom']): ?>
        <tr>
            <td width="14" style="border: solid transparent; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
            </td>
            <td width="29" bgcolor="white" style="border: solid white; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
            </td>
            <td colspan="3" width="510" bgcolor="white" style="border: solid white; border-width: 5px 0;">
                <div class="content">
                    <p style="line-height: 1.2em;"><font face="arial" size="2" color="#5E4A3E">
                            <?php echo stripslashes(nl2br($row_retSessionEmail['email_message_bottom'])); ?>
                        </font></p>
                </div>
            </td>
            <td width="29" bgcolor="white" style="border: solid white; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
            </td>
            <td width="14" style="border: solid transparent; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
            </td>
        </tr>
    <?php endif; ?>
    <?php if (isset($facilitator_firstname) && $facilitator_firstname): ?>
        <tr>
            <td width="14" style="border: solid transparent; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="12" alt=""/>
            </td>
            <td width="29" bgcolor="white" style="border: solid white; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="12" alt=""/>
            </td>
            <td width="510" bgcolor="white" style="border: solid white; border-width: 5px 0;" colspan="3">
                <div class="content">
                    <h2 style="font-weight: normal; margin: 0;">
                        <font face="arial" size="5"
                              color="<?php echo(isset($row_retSessionEmail['colour_text']) ? $row_retSessionEmail['colour_text'] : '#E51937'); ?>">
                            <?php echo stripslashes(htmlspecialchars($facilitator_firstname)) . ' ' . stripslashes(htmlspecialchars($facilitator_lastname)); ?>
                        </font></h2>
                </div>
            </td>
            <td width="29" bgcolor="white" style="border: solid white; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
            </td>
            <td width="14" style="border: solid transparent; border-width: 5px 0;">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
            </td>
        </tr>
        <tr>
            <td width="14">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
            </td>
            <td width="29" bgcolor="white">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
            </td>
            <td width="145" bgcolor="white" style="vertical-align: top;">
                <p style="margin: 0;"><font face="arial" size="2" color="#5E4A3E">
                        <strong>Facilitator</strong>
                    </font></p>
            </td>
            <td width="200" bgcolor="white" style="vertical-align: top;">
                <div class="content" align="center"
                     style="border: dotted <?php echo(isset($row_retSessionEmail['colour_border']) ? $row_retSessionEmail['colour_border'] : '#E21F3C'); ?>; border-width: 0 2px;">
                    <p style="margin: 0;" align="center"><font face="arial" size="2" color="#5E4A3E">
                            <strong>Email:</strong> <?php echo $facilitator_email; ?>
                        </font></p>
                </div>
            </td>
            <td width="145" bgcolor="white" style="vertical-align: top;">
                <div class="content" align="center">
                    <p style="margin: 0;" align=""><font face="arial" size="2" color="#5E4A3E">
                            <strong>Phone:</strong> <?php echo $facilitator_phone; ?>
                        </font></p>
                </div>
            </td>
            <td width="29" bgcolor="white">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
            </td>
            <td width="14">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
            </td>
        </tr>
        <tr>
            <td width="14">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
            </td>
            <td colspan="5" bgcolor="white">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="548" height="2" alt=""/>
            </td>
            <td width="14">
                <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
            </td>
        </tr>
    <?php endif; ?>
    <tr>
        <td colspan="7">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="596" height="7" alt=""/>
        </td>
    </tr>
    <tr>
        <td width="14">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="14" alt=""/>
        </td>
        <td width="29" style="border: solid transparent; border-width: 5px 0;">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
        <td colspan="3" width="510">
            <div class="content">
                <p style="line-height: 1.3em; margin: 0;"><font face="arial" size="2" color="#5E4A3E">
                        <em>Powered by InsiderFocus</em>
                </p></font>
            </div>
        </td>
        <td width="29" style="border: solid transparent; border-width: 5px 0;">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="29" alt=""/>
        </td>
        <td width="14">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="7" alt=""/>
        </td>
    </tr>
    <tr width="596">
        <td colspan="7">
            <img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="596" height="7" alt=""/>
        </td>
    </tr>
</table>
<img src="<?php echo $ADMIN_URL; ?>images/new_layout/blank.png" width="600" height="40" alt=""/>
<?php if (isset($preview) && $preview): ?>
    <div style="width: 596px; text-align: right; margin: -20px auto 10px;">
        <form id="form1" name="form1" method="post"
              action="<?php echo $ADMIN_URL; ?>participant-email-template-admin.php?session_id=<?php echo $session_id; ?>&email_type_id=<?php echo $email_type_id; ?>&update=1">
            <input type="image" src="<?php echo $ADMIN_URL; ?>images/email_back.png" alt="Back" name="btnPrevSubmit"/>
        </form>
    </div>
<?php endif; ?>
</div>
</body>
</html>