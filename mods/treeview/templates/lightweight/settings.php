<?php
/*  Threaded Tree View Settings for the Lightweight Template
 *
 *  Please do NOT remove any variables listed below.  If the variable is not
 *  needed, simply leave empty quotes.
 *
 */
if (!defined("PHORUM")) return;

$h_width["lightweight"] = 21; // default horizontal line width
$v_height["lightweight"] = 35; // default vertical line height

$line_width["lightweight"] = 1; // default line thickness
$line_color["lightweight"] = "Black"; // default line color

$customizations["lightweight"] = array (
    "all_children_messages_modifier" => // php code to run for all children messages
        '',
    "last_message_with_children_modifier" => // php code to run for the last message in a tree, if that message has children
        '',
    "last_message_without_children_modifier" => // php code to run for the last message in a tree, if that message does not have children
        '',
    "all_thread_starter_messages_with_children_modifier" => // php code to run for the first message in the tree.
        '',
    );
?>
