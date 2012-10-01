<?php
/*  Threaded Tree View Settings for the East Link Template
 *
 *  Please do NOT remove any variables listed below.  If the variable is not
 *  needed, simply leave empty quotes.
 *
 */
if (!defined("PHORUM")) return;

$h_width["eastlink"] = 16; // default horizontal line width
$v_height["eastlink"] = 30; // default vertical line height

$line_width["eastlink"] = 1; // default line thickness
$line_color["eastlink"] = "#345487"; // default line color

$customizations["eastlink"] = array (
    "all_children_messages_modifier" => // php code to run for all children messages
        '',
    "last_message_with_children_modifier" => // php code to run for the last message in a tree, if that message has children
        'if (phorum_page != "read")
        {
            $messages[$key]["v_height"] += 3;
            $messages[$key]["v_margin"] += 3;
        }',
    "last_message_without_children_modifier" => // php code to run for the last message in a tree, if that message does not have children
        'if (phorum_page != "read")
        {
            $messages[$key]["v_height"] += 3;
            $messages[$key]["v_margin"] += 3;
        }',
    "all_thread_starter_messages_with_children_modifier" => // php code to run for the first message in the tree
        '',
    );
?>
