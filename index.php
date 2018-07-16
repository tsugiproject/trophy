<?php
// https://github.com/tsugiproject/trophy
require_once "../config.php";

use \Tsugi\Core\LTIX;

// Handle all forms of launch
$LTI = LTIX::requireData();

// Render view
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();

$OUTPUT->welcomeUserCourse();

if ( ! $LTI->user->instructor ) {
   echo('<p><i class="fa fa-trophy"></i>');
   echo(' You earned a trophy!</p>');
   $LTI->result->gradeSend(0.95, false);
}

$OUTPUT->footer();
