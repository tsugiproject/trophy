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

$origin = $LTI->ltiJWTClaim("https://purl.imsglobal.org/spec/lti/claim/origin");

if ( $LTI->user->instructor ) {
    echo("<p>Instructors can't send grades with LTI so here is a LAUNCH dump</p>\n");
    echo("<pre>\n");var_dump($LTI);echo("</pre>\n");
    echo("<p>All the 'cooked' LTI parameters</p>\n");
    echo("<pre>\n");var_dump($LTI->ltiParameterArray());echo("</pre>\n");
} else {
   echo('<center><i class="fa fa-trophy fa-5x" style="color: blue;"></i>');
   echo('<br/>You earned a trophy!<br/>');
   $LTI->result->gradeSend(0.95, false);
   $lastSendTransport = $LTI->result->lastSendTransport;
   if ( $lastSendTransport ) {
       echo('And your grade was sent using '.htmlentities($lastSendTransport).'<br>');
    } else {
        echo('And your grade was stored locally<br>');
    }
    echo("\n</center>\n");
}

if ( $origin ) {
$postjson = new \stdClass();
$postjson->subject = "org.imsglobal.lti.put_data";
$postjson->key = 'answer';
$postjson->value = 42.0;
$postjson->message_id = 42;
$postset = json_encode($postjson);

$postjson = new \stdClass();
$postjson->subject = "org.imsglobal.lti.get_data";
$postjson->key = 'answer';
$postjson->message_id = 42;
$postget = json_encode($postjson);

$postjson->subject = "org.imsglobal.lti.get_data";
$postjson->key = 'state';
$postjson->message_id = 42;
$poststate = json_encode($postjson);
?>
<script>
window.addEventListener('message', function (e) {
    console.log('Trophy received message');
    console.log(e.data);
    console.log((e.source == parent ? 'Source parent' : 'Source not parent '+e.source), '/', 
                (e.origin == '<?= $origin ?>' ? 'Origin match' : 'Origin mismatch '+e.origin));
});
console.log('trophy sending org.imsglobal.lti.put_data');
parent.postMessage(<?= $postset ?>, '<?= $origin ?>');
console.log('Do I need a setTimeout here??');
console.log('trophy sending org.imsglobal.lti.get_data');
parent.postMessage(<?= $postget ?>, '<?= $origin ?>');
console.log('trophy sending reading state');
parent.postMessage(<?= $poststate ?>, '<?= $origin ?>');

</script>
<?php
}


$OUTPUT->footer();
