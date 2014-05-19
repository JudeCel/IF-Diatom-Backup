<?php
require_once("../Connections/ifs.php");
?>

<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gte IE 9]>
<html class="ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<?php
/*
echo $PORT."<BR/>";

echo $BASE_FS_PATH."<BR/>";
echo $FS_PATH."<BR/>";

echo $BASE_URL_PATH."<BR/>";
echo $URL_PATH."<BR/>";

echo $BASE_URL."<BR/>";

echo $MYSQL_USER."<BR/>";
echo $MYSQL_PASS."<BR/>";
echo $MYSQL_DATABASE."<BR/>";
*/
?>
<head>
    <meta charset="utf-8">

    <title><?php echo $session_name; ?> Green Room</title>

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
         More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

    <link rel="stylesheet" href="<?php echo $CHAT_ROOM_URL ?>css/screen_green.css"/>
    <link rel="stylesheet" href="../css/misc.css"/>
    <link rel="stylesheet" href="../boilerplate/css/style.css"/>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" type="text/css" href="../js/fancybox/jquery.fancybox-1.3.4.css" media="screen"/>

    <script type="text/javascript">
        var session_id = <?php echo $session_id; ?>,
            user_id = <?php echo $user_id; ?>;
    </script>

    <!-- All JavaScript at the bottom, except this Modernizr build.
         Modernizr enables HTML5 elements & feature detects for optimal performance.
         Create your own custom Modernizr build: www.modernizr.com/download/ -->
    <script src="../boilerplate/js/libs/modernizr-2.5.3.min.js"></script>
    <script type="text/javascript" src="<?php echo $BASE_URL ?>socket.io/socket.io.js"></script>

    <script type="text/javascript" src="<?php echo $CHAT_ROOM_URL ?>resources/raphael/raphael.js"></script>

    <script type="text/javascript" src="<?php echo $CHAT_ROOM_URL ?>js/utilities.js"></script>

    <script type="text/javascript" src="<?php echo $CHAT_ROOM_URL ?>js/avatarsAsSets.js"></script>
    <script type="text/javascript" src="<?php echo $CHAT_ROOM_URL ?>classes/avatarRenderer.js"></script>
    <script type="text/javascript" src="<?php echo $CHAT_ROOM_URL ?>classes/avatarChooser.js"></script>

</head>
<body id="greenroom">
<?php if (isset($preview) && $preview): ?>
    <div class="notification">
        This is a preview of your updates
    </div>
<?php endif; ?>

<div id="session_container" class="container">
    <div class="inner">
        <header class="row-fluid">

            <div id="branding" class="span9">
                <div class="inner">
                    <h1><?php echo $session_name; ?> Green Room</h1>

                    <div class="buttons">
                        <a href="<?php echo $x_close_url; ?>" class="close_btn" title="Exit Green Room"></a>
                        <a href="../help.php?page=green_room" class="question" title="Help"></a>
                    </div>

                    <div class="info">
                        <a href="profile.php?session_id=<?php echo $session_id ?>">Update Your Profile</a>
                        <a href="../Terms_and_Conditions.pdf" class="last" target="_blank">Terms and Conditions</a>
                    </div>

                </div>
            </div>

            <div id="logo" class="span3">
                <a href="index.php?session_id=<?php echo $session_id ?>" title="Insiderfocus Logo">
                    <img src="<?php echo($chatroom_logo_url ? '../' . $chatroom_logo_url : '../images/logoDefaultInsiderfocus.jpg');  ?>"
                         alt="Insiderfocus logo"/>
                </a>
            </div>
        </header>

        <div id="content" class="row-fluid" role="main">
            <div id="information" class="span6">

                <div id="intro" class="section">
                    <?php if (isset($green_room_session['greeting']) && $green_room_session['greeting']): ?>
                        <p><?php echo stripslashes(nl2br($green_room_session['greeting'])); ?></p>
                    <?php endif; ?>
                </div>

                <div id="overview" class="section" role="contentinfo">
                    <h2>Overview</h2>

                    <?php if (isset($green_room_session['overview']) && $green_room_session['overview']): ?>
                        <p><?php echo stripslashes(nl2br($green_room_session['overview'])); ?></p>
                    <?php endif; ?>
                </div>

                <aside role="complementary">
                    <h2>Session Details</h2>

                    <div class="info">

                        <div class="session">
                            <h3>Session Start Time</h3>

                            <p><?php echo $start_date; ?></p>
                        </div>

                        <div class="spacing"></div>

                        <div class="session">
                            <h3>Session End Time</h3>

                            <p><?php echo $end_date; ?></p>
                        </div>

                        <div class="spacing"></div>

                        <?php
                        $enter_chat_room_url = $BASE_URL . "?id=" . $user_id . "&sid=" . $session_id
                        ?>

                        <a href="<?php echo $enter_chat_room_url ?>" class="enter">Enter Chat Room</a>
                    </div>
                </aside>

            </div>

            <div class="span6" id="identity">
                <div class="inner">

                    <div class="block">
                        <h2 class="legend">Customise Your Avatar <span class="corner"></span></h2>

                        <div id="avatar" style="height: 265px;"></div>
                    </div>

                    <figure id="video_player" class="block last">
                        <figcaption class="legend">Video Gallery <span class="corner"></span></figcaption>

                        <div class="row-fluid">
                            <div class="span12">
                                <div class="row-fluid" id="player_container">
                                    <a href="http://youtu.be/IdCByVloccU" class="video_btn span3" id="video_1"><span>Intro</span></a>
                                </div>
                            </div>
                        </div>
                    </figure>

                </div>
            </div>
        </div>
        <!-- Content -->
    </div>
    <!-- Inner -->
</div>
<!-- Container  -->

<?php if (isset($preview) && $preview): ?>
    <div class="back_button">
        <form id="form1" name="form1" method="post"
              action="<?php echo $ADMIN_URL ?>green_room_template_admin.php?session_id=<?php echo $session_id; ?>">
            <input type="image" src="<?php echo $ADMIN_URL ?>images/email_back.png" alt="Back" name="btnPrevSubmit"/>
        </form>
    </div>
<?php endif; ?>

<!-- JavaScript at the bottom for fast page loading -->

<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>window.jQuery || document.write("<script src=\"<?php echo $ADMIN_URL ?>boilerplate/js/libs/jquery-1.7.1.min.js\"><\/script>")</script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>

<!-- Google's CDN's SWFObject -->
<script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

<!-- Handlebars -->
<script src="<?php echo $CHAT_ROOM_URL ?>js/handlebars-1.0.0.beta.6.js"></script>

<!-- scripts concatenated and minified via build script -->
<script src="<?php echo $ADMIN_URL ?>boilerplate/js/plugins.js"></script>

<script type="text/javascript" src="../js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="../js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>

<script type="text/javascript">
    //	set up some global variables (from ifs.php -> config.json)
    window.PORT = "<?php echo $PORT; ?>";
    window.DOMAIN = "<?php echo $DOMAIN; ?>";
</script>
<script src="<?php echo $CHAT_ROOM_URL ?>js/script.js"></script>
<!-- end scripts -->
</body>
</html>
