<a class="buttons darker" href="IFS/index.php?session_id=<?php echo $session_id; ?>"><span class="icon play"></span>Enter Green Room</a>
<a class="buttons darker" href="http://<?php echo $DOMAIN;?>:<?php echo $PORT;?>/?id=<?php echo $user_id; ?>&sid=<?php echo $session_id; ?>"><span class="icon play"></span>Enter Chat Room</a>
<a class="buttons darker last" href="session-close.php?session_id=<?php echo $session_id; ?>"><span class="icon <?php echo ($status_id == 2 ? 'play' : 'close'); ?>"></span><?php echo ($status_id == 2 ? 'Open' : 'Close'); ?> This Session</a>
