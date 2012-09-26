<?php
/*  Threaded Tree View Settings for the Classic Template
 *
 *  Please do NOT remove any variables listed below.  If the variable is not
 *  needed, simply leave empty quotes.
 *
 */
if (!defined("PHORUM")) return;

$h_width["classic"] = 15; // default horizontal line width
$v_height["classic"] = 42; // default vertical line height

$line_width["classic"] = 1; // default line thickness
$line_color["classic"] = "Black"; // default line color

$customizations["classic"] = array (
    "all_children_messages_modifier" => // php code to run for all children messages
        '',
    "last_message_with_children_modifier" => // php code to run for the last message in a tree, if that message has children
        '$messages[$key]["v_margin"] -= 10;',
    "last_message_without_children_modifier" => // php code to run for the last message in a tree, if that message does not have children
        '$messages[$key]["v_margin"] -= 10;',
    "all_thread_starter_messages_with_children_modifier" => // php code to run for the first message in the tree
        '',
    );
?>
