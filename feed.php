<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2006  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of either the current Phorum License (viewable at     //
//   phorum.org) or the Phorum License that was distributed with this file    //
//                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

define("phorum_page", "feed");

include_once("./common.php");
include_once("./include/format_functions.php");
include_once("./include/feed_functions.php");

// somehow we got to a folder
if(!empty($PHORUM["folder_flag"]) && $PHORUM["forum_id"] != $PHORUM["vroot"]){
    exit();
}

// get forums this user can read if no forum_id passed
if(empty($PHORUM["forum_id"])){
    $forums = phorum_db_get_forums();
} else {
    // its cheap to copy this even though there is more than needed in it
    $forums[$PHORUM["forum_id"]] = $PHORUM;
}

// checking read-permissions
if(empty($forums) || !phorum_check_read_common()) {
  return;
}

// grab the data from cache if we can
//  $cache = phorum_cache_get("feed", $_SERVER["REQUEST_URI"]);

if(!empty($cache)){

    // extract the two members from cache
    list($data, $content_type) = $cache;
    
} else {
    
    // if it wasn't in cache, we need to make it
    
    // init array
    $messages = array();
    
    // check if this is a thread subscription
    $thread = (isset($PHORUM["args"][1])) ? (int)$PHORUM["args"][1] : 0;
    
    // check if we are getting replies 
    $no_replies = (empty($PHORUM["args"]["replies"])) ? true : false;
    
    // check the feed type 
    $feed_type = (empty($PHORUM["args"]["type"])) ? "rss" : $PHORUM["args"]["type"];

    // generate list of forum ids to grab data for
    $forum_ids = array_keys($forums);

    // get messages
    $messages = phorum_db_get_recent_messages(30, $forum_ids, $thread, $no_replies);

    // remove users from messages array
    unset($messages["users"]);
    
    // run read hooks to get everything formatted
    $messages = phorum_hook("read", $messages);
    $messages = phorum_format_messages($messages);

    // set up the feed specifics based on the info we are getting
    if($thread && $PHORUM["forum_id"]){
        $feed_url = phorum_get_url(PHORUM_FOREIGN_READ_URL, $PHORUM["forum_id"], $thread, $thread);
        $feed_title = $message[$thread]["subject"];
        $feed_description = strip_tags($message[$thread]["body"]);
    } elseif($PHORUM["forum_id"]){
        $feed_url = phorum_get_url(PHORUM_LIST_URL);
        $feed_title = $PHORUM["DATA"]["TITLE"]." - ".$PHORUM["DATA"]["NAME"];
        $feed_description = strip_tags($PHORUM["DATA"]["DESCRIPTION"]);
    } else {
        $feed_url = phorum_get_url(PHORUM_INDEX_URL);
        $feed_title = $PHORUM["DATA"]["TITLE"];
        $feed_description = (!empty($PHORUM["description"])) ? $PHORUM["description"] : "";
    }

    
    switch($feed_type) {

        case "html":
            $data = phorum_feed_make_html($messages, $forums, $feed_url, $feed_title, $feed_description);
            $content_type = "text/html";
            break;
            
        case "js":
            $data = phorum_feed_make_js($messages, $forums, $feed_url, $feed_title, $feed_description);
            $content_type = "text/javascript";
            break;
            
        case "atom":
            $data = phorum_feed_make_atom($messages, $forums, $feed_url, $feed_title, $feed_description);
            $content_type = "application/xml";
            break;

        default:
            $data = phorum_feed_make_rss($messages, $forums, $feed_url, $feed_title, $feed_description);
            $content_type = "application/xml";
            break;
        
    }
        
    // stick the xml in cache for 5 minutes for future use
    phorum_cache_put("feed", $_SERVER["REQUEST_URI"], array($data, $content_type, 600));

}

// output the proper header and the data
header("Content-type: $content_type;");
echo $data;


?>
