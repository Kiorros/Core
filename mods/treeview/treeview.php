<?php

if (!defined("PHORUM")) return;

function mod_treeview($messages)
{
    $PHORUM = $GLOBALS["PHORUM"];

    // grab the current template
    $template = $PHORUM["template"];
    
    // remove the default marker
    $GLOBALS["PHORUM"]["DATA"]["marker"]="";
    
    // pull the custom settings for each template
    $d = dir( "./mods/treeview/templates" );
    while ( false !== ( $entry = $d->read() ) ) {
        if ( $entry != "." && $entry != ".." && file_exists( "./mods/treeview/templates/$entry/settings.php" ) ) {
            include "./mods/treeview/templates/$entry/settings.php";
        }
    }

    if ((phorum_page=='list' && $PHORUM["threaded_list"]) || (phorum_page=='read' && $PHORUM["threaded_read"])){
        
        // we have to loop and figure out 
        // what we know about the message in the array       
        foreach($messages as $message){
            // only do this part if this is a child message
            if($message["parent_id"]){
                $parents[$message["parent_id"]]=$message['message_id'];
                if (empty($parent_counts[$message["parent_id"]])) {
                    $parent_counts[$message["parent_id"]] = 1;
                } else {
                    $parent_counts[$message["parent_id"]] ++;
                }
            }
        }

        // now we loop and can set the lines right based on
        // what we learned in the first loop
        foreach($messages as $key=>$message){
            
            $messages[$key]["h_width"] = 0;
            $messages[$key]["v_height"] = 0;
            $messages[$key]["v_margin"] = 0;
            $messages[$key]["indent"] = "";
            
            // only do this part if this is a child message
            if($message["parent_id"]){
                if(isset($parents[$message["parent_id"]])){
                    if (empty($counts[$message["parent_id"]])) {
                        $counts[$message["parent_id"]] = 1;
                    } else {
                        $counts[$message["parent_id"]] ++;
                    }
                    $parent_id = $message["parent_id"];
                    while (!empty($messages[$parent_id])) {
                        if (empty($messages[$parent_id]["parent_id"])) break;
                        $counts[$messages[$parent_id]["parent_id"]] ++;
                        $parent_id = $messages[$parent_id]["parent_id"];
                    }
                    
                    // this is about my position in my parent's list
                    $messages[$key]["h_width"] = $h_width[$template];
                    
                    // run the modifier code for the current template
                    eval($customizations[$template]["all_children_messages_modifier"]);
                    
                    // this is about if I have children
                    if(isset($parents[$message["message_id"]])){

                        // this is setting our last message in the tree
                        // we have children
                        $multiplier = $counts[$message["parent_id"]];
                        $messages[$key]["v_height"] = $v_height[$template] * $multiplier;
                        $messages[$key]["v_margin"] = $v_height[$template] * $multiplier - 7;
                        
                        // run the modifier code for the current template
                        eval($customizations[$template]["last_message_with_children_modifier"]);
                    } else {

                        // this is setting our last message in the tree
                        // we have no children
                        $multiplier = $counts[$message["parent_id"]];
                        $messages[$key]["v_height"] = $v_height[$template] * $multiplier;
                        $messages[$key]["v_margin"] = $v_height[$template] * $multiplier - 7;
                        
                        // run the modifier code for the current template
                        eval($customizations[$template]["last_message_without_children_modifier"]);
                    }
                }
            } else {
                if (!empty($parents[$message["message_id"]]))
                    // run the modifier code for the current template
                    eval($customizations[$template]["all_thread_starter_messages_with_children_modifier"]);
            }
        }
    }
    
    return $messages;
}

function mod_treeview_start_output() {
    if (phorum_page == "list"
        || phorum_page == "read") {
        global $PHORUM;
        if (empty($PHORUM["treeview"]["profile_set"])) ob_start();
    }
}

function mod_treeview_end_output() {
    global $PHORUM;

    if (!empty($PHORUM["treeview"]["profile_set"])) return;

    // if either line color or line thickness has not been set on the settings
    // page, we will need to include each template's custom settings
    if (empty($PHORUM["mod_treeview"]["line_color"]) || empty($PHORUM["mod_treeview"]["line_width"])) {
        // grab the current template
        $template = $PHORUM["template"];
        
        // pull the custom settings for each template
        $d = dir( "./mods/treeview/templates" );
        while ( false !== ( $entry = $d->read() ) ) {
            if ( $entry != "." && $entry != ".." && file_exists( "./mods/treeview/templates/$entry/settings.php" ) ) {
                include "./mods/treeview/templates/$entry/settings.php";
            }
        }
    }
    
    if (empty($PHORUM["mod_treeview"]["line_color"])) {
        if (empty($line_color[$template])) {
            $PHORUM["DATA"]["mod_treeview"]["line_color"] = "Black";
        } else {
            $PHORUM["DATA"]["mod_treeview"]["line_color"] = $line_color[$template];
        }
    } else {
        $PHORUM["DATA"]["mod_treeview"]["line_color"] = $PHORUM["mod_treeview"]["line_color"];
    }
    
    // grab the line thickness, first from settings, then from the current
    // template, then finally from the default 1px
    if (empty($PHORUM["mod_treeview"]["line_width"])) {
        if (empty($line_color[$template])) {
            $PHORUM["DATA"]["mod_treeview"]["line_width"] = 1;
        } else {
            $PHORUM["DATA"]["mod_treeview"]["line_width"] = $line_width[$template];
        }
    } else {
        $PHORUM["DATA"]["mod_treeview"]["line_width"] = $PHORUM["mod_treeview"]["line_width"];
    }
    
    if (!empty($PHORUM["threaded_list"]) && phorum_page == "list") {
        ob_end_clean();
        $PHORUM["treeview"]["profile_set"] = 1;
        phorum_output("treeview::list_threads");
    } elseif ($PHORUM["threaded_read"] == 1 && phorum_page == "read") {
        ob_end_clean();
        $PHORUM["treeview"]["profile_set"] = 1;
        $templates[] = "treeview::read_threads";
        // {REPLY_ON_READ} is set when message replies are done on
        // the read page. The template can use this to add the
        // #REPLY anchor to the page. This way, the browser can jump
        // to the editor when clicking a reply link.
        $PHORUM["DATA"]["REPLY_ON_READ"] = !empty($PHORUM["reply_on_read_page"]);
    
        if (isset($PHORUM["reply_on_read_page"]) && $PHORUM["reply_on_read_page"]) {
        
            // Never show the reply box if the message is closed.
            if(!empty($thread_is_closed)) {
        
                $PHORUM["DATA"]["OKMSG"] = $PHORUM["DATA"]["LANG"]["ThreadClosed"];
                $templates[] = "message";
        
            } else {
                // Prepare the arguments for the posting.php script.
                $goto_mode = "reply";
                if (isset($PHORUM["args"]["quote"]) && $PHORUM["args"]["quote"]) {
                    $goto_mode = "quote";
                }
        
                $PHORUM["postingargs"] = array(
                    1 => $goto_mode,
                    2 => $PHORUM["DATA"]["MESSAGE"]["message_id"],
                    "as_include" => true
                );
        
                if (isset($PHORUM["postingargs"]["as_include"]) && isset($templates))
                    $templates[] = $PHORUM["posting_template"];
            }
        }    
        phorum_output($templates);
    }
}
?>