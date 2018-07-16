<?php

$REGISTER_LTI2 = array(
"name" => "Trophy",
"FontAwesome" => "fa-trophy",
"short_name" => "Trophy",
"description" => "This tool gives students a grade of 0.95 for a successful launch.
",
    "messages" => array("launch", "launch_grade"),
    "privacy_level" => "anonymous",  // anonymous, name_only, public
    "license" => "Apache",
    "languages" => array(
        "English"
    ),
    "analytics" => array(
        "internal"
    ),
    "source_url" => "https://github.com/tsugitools/youtube",
    // For now Tsugi tools delegate this to /lti/store
    "placements" => array(
        /*
        "course_navigation", "homework_submission",
        "course_home_submission", "editor_button",
        "link_selection", "migration_selection", "resource_selection",
        "tool_configuration", "user_navigation"
        */
    ),
    "screen_shots" => array(
/*
        "store/screen-01.png",
        "store/screen-02.png",
        "store/screen-03.png",
        "store/screen-views.png",
        "store/screen-analytics.png"
*/
    )
);
