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

// http://www.imsglobal.org/spec/lti-ags/v2p0#gradingprogress
$gradingProgress = U::get($_POST, LTI13::GRADING_PROGRESS);
// http://www.imsglobal.org/spec/lti-ags/v2p0#activityprogress
$activityProgress = U::get($_POST, LTI13::ACTIVITY_PROGRESS);

if ( count($_POST) > 0 && is_string($grade) ) {
   $debug_log = array();
   $extra = array(LTI13::LINEITEM_COMMENT => $comment);
   if ( $LTI->isLTI13() && $activityProgress ) $extra[LTI13::ACTIVITY_PROGRESS] = $activityProgress;
   if ( $LTI->isLTI13() && $gradingProgress ) $extra[LTI13::GRADING_PROGRESS] = $gradingProgress;
   $LTI->result->gradeSend($grade, false, $debug_log, $extra);
   $lastSendTransport = $LTI->result->lastSendTransport;
   $_SESSION['sent'] = true;
   $_SESSION['grade'] = $grade;
   $_SESSION['comment'] = $comment;
   $_SESSION[LTI13::GRADING_PROGRESS] = $gradingProgress;
   $_SESSION[LTI13::ACTIVITY_PROGRESS] = $activityProgress;
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
$gradingProgress = U::get($_SESSION, LTI13::GRADING_PROGRESS);
$activityProgress = U::get($_SESSION, LTI13::ACTIVITY_PROGRESS);

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
// $gradingProgress = U::get($_SESSION, LTI13::GRADING_PROGRESS);
// $activityProgress = U::get($_SESSION, LTI13::ACTIVITY_PROGRESS);
function doOption($option, $current)
{
    echo('<option value="'.$option.'"');
    if ( $option == $current ) echo(' selected');
    echo('>'.$option."</option>\n");
}
?>
<form method="post">
<input type="text" name="grade"
value=" <?= $grade ?>"/> Grade<br/>
<input type="text" name="comment"
value=" <?= $comment ?>"/> Comment</br/>
<?php if ( $LTI->isLTI13() ) { ?>
<select name="<?= LTI13::GRADING_PROGRESS ?>">
<option value="">-- select <?= LTI13::GRADING_PROGRESS ?> (optional)---</option>
<?php
doOption(LTI13::GRADING_PROGRESS_FULLYGRADED, $gradingProgress);
doOption(LTI13::GRADING_PROGRESS_PENDING, $gradingProgress);
doOption(LTI13::GRADING_PROGRESS_PENDINGMANUAL, $gradingProgress);
doOption(LTI13::GRADING_PROGRESS_FAILED, $gradingProgress);
doOption(LTI13::GRADING_PROGRESS_NOTREADY, $gradingProgress);
?>
</select><br/>
<select name="<?= LTI13::ACTIVITY_PROGRESS ?>">
<option value="">-- select <?= LTI13::ACTIVITY_PROGRESS ?> (optional)---</option>
<?php
doOption(LTI13::ACTIVITY_PROGRESS_INITIALIZED, $activityProgress);
doOption(LTI13::ACTIVITY_PROGRESS_STARTED, $activityProgress);
doOption(LTI13::ACTIVITY_PROGRESS_INPROGRESS, $activityProgress);
doOption(LTI13::ACTIVITY_PROGRESS_SUBMITTED, $activityProgress);
doOption(LTI13::ACTIVITY_PROGRESS_COMPLETED, $activityProgress);
?>
</select><br/>
<?php } ?>
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

/*

gradingProgress MUST be used to indicate to the platform the status of the grading process, including allowing to inform when human intervention is needed.

The gradingProgress property of a score must have one of the following values:

    FullyGraded: The grading process is completed; the score value, if any, represents the current Final Grade;
    Pending: Final Grade is pending, but does not require manual intervention; if a Score value is present, it indicates the current value is partial and may be updated.
    PendingManual: Final Grade is pending, and it does require human intervention; if a Score value is present, it indicates the current value is partial and may be updated during the manual grading.
    Failed: The grading could not complete.
    NotReady: There is no grading process occurring; for example, the student has not yet made any submission.

It is up to the tool to determine the appropriate gradingProgress value. A tool platform MAY ignore scores that are not FullyGraded as those have to be considered partial grades.

activityProgress MUST be used to indicate to the tool platform the status of the user towards the activity's completion.

The activityProgress property of a score MUST have one of the following values:

    Initialized – the user has not started the activity, or the activity has been reset for that student.
    Started – the activity associated with the line item has been started by the user to which the result relates.
    InProgress - the activity is being drafted and is available for comment.
    Submitted - the activity has been submitted at least once by the user but the user is still able make further submissions.
    Completed – the user has completed the activity associated with the line item.

    It is up to the tool to determine the appropriate 'activityProgress' value. A tool platform MAY ignore statuses it does not support.
*/

