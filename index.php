<?php
// https://github.com/tsugiproject/trophy
require_once "../config.php";

use \Tsugi\Util\U;
use \Tsugi\Util\LTI13;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\Output;

// Handle all forms of launch
$LTI = LTIX::requireData();

$grade = U::get($_POST, 'grade');
$comment = U::get($_POST, 'comment');

if ( count($_POST) > 0 && is_string($grade) ) {
   $extra = array(LTI13::LINEITEM_COMMENT => $comment);
   $debug_log = array();
   $LTI->result->gradeSend($grade, false, $debug_log, $extra);
   $lastSendTransport = $LTI->result->lastSendTransport;
   $_SESSION['sent'] = true;
   $_SESSION['grade'] = $grade;
   $_SESSION['comment'] = $comment;
   $_SESSION['transport'] = $lastSendTransport;
   $_SESSION['debug_log'] = $debug_log;
   header("Location: ".addSession("index.php"));
   return;
}

$sent = U::get($_SESSION, 'sent');
$grade = U::get($_SESSION, 'grade', 0.95);
$comment = U::get($_SESSION, 'comment', '');
$lastSendTransport = U::get($_SESSION, 'transport');
$debug_log = U::get($_SESSION, 'debug_log');

// Render view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();

$OUTPUT->welcomeUserCourse();

if ( $LTI->user->instructor ) {
    echo("<p>Instructors can't send grades with LTI so here is a LAUNCH dump</p>\n");
    echo('<pre>'."\n");
    echo(htmlentities(Output::safe_var_dump($LTI)));
    echo("</pre>\n");
    echo("<p>All the 'cooked' LTI parameters</p>\n");
    echo("<pre>\n");var_dump($LTI->ltiParameterArray());echo("</pre>\n");
    $OUTPUT->footer();
    return;
}
?>
<form method="post">
<input type="text" name="grade"
value=" <?= $grade ?>"/> Grade<br/>
<input type="text" name="comment"
value=" <?= $comment ?>"/> Comment</br/>
<input type="submit">
</form>
<?php

if ( $sent ) {
   echo('<center><i class="fa fa-trophy fa-5x" style="color: blue;"></i>');
   echo('<br/>You earned a trophy!<br/>');
   if ( $lastSendTransport ) {
       echo('And your grade was sent using '.htmlentities($lastSendTransport).'<br>');
    } else {
        echo('And your grade was stored locally<br>');
    }
    echo("\n</center>\n");
    echo('<button id="toggle">Toggle Debug Log</button>'."\n");
    echo('<pre id="detail" style="display:none;">'."\n");
    echo(htmlentities(Output::safe_var_dump($debug_log)));
    echo("</pre>\n");
}

$OUTPUT->footerStart();
?>
<script>
$(document).ready(function() {
   $("#toggle").click(function(){
       $("#detail").toggle();
   });
});
</script>
<?php

$OUTPUT->footerEnd();
