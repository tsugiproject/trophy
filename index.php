<?php
// https://github.com/tsugiproject/trophy
require_once "../config.php";

use \Tsugi\Util\LTI13;
use \Tsugi\Core\LTIX;

// Handle all forms of launch
$LTI = LTIX::requireData();

// Render view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();

$OUTPUT->welcomeUserCourse();

if ( $LTI->user->instructor ) {
    echo("<p>Instructors can't send grades with LTI so here is a LAUNCH dump</p>\n");
    echo("<pre>\n");var_dump($LTI);echo("</pre>\n");
    echo("<p>All the 'cooked' LTI parameters</p>\n");
    echo("<pre>\n");var_dump($LTI->ltiParameterArray());echo("</pre>\n");
} else {
   echo('<center><i class="fa fa-trophy fa-5x" style="color: blue;"></i>');
   echo('<br/>You earned a trophy!<br/>');
   $debug_log = false;
   $extra = array(LTI13::LINEITEM_COMMENT => "Trophy time");
   $LTI->result->gradeSend(0.95, false, $debug_log, $extra);
   $lastSendTransport = $LTI->result->lastSendTransport;
   if ( $lastSendTransport ) {
       echo('And your grade was sent using '.htmlentities($lastSendTransport).'<br>');
    } else {
        echo('And your grade was stored locally<br>');
    }
    echo("\n</center>\n");
}


$OUTPUT->footer();
