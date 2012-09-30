<?php
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2011  Phorum-Support.de - Thomas Seifert                   //
//   http://www.phorum-support.de - thomas.seifert@phorum-support.de          //
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

if (!defined("PHORUM")) return;

// This addon will be called for doing the actual switching between users.
function phorum_mod_block_user_addon()
{
    global $PHORUM;
    
    if(!phorum_mod_block_user_check_allowed()) {
    	$redir_url = phorum_get_url(PHORUM_INDEX_URL);
    	phorum_redirect_by_url($redir_url);
    	exit();
    }
    $PHORUM["DATA"]["HEADING"] = $PHORUM['DATA']['LANG']['mod_block_user']['BlockUser'];
    
    phorum_build_common_urls();
    $PHORUM['DATA']['URL']['ACTION']=phorum_get_url(PHORUM_ADDON_URL);

    if (!count($_POST)) {
    	$PHORUM['DATA']['SHOW_FORM']=1;
	    if (empty($PHORUM['args']['uid'])) trigger_error(
	        'Illegal call to the block user mod addon handler: ' .
	        'missing parameter "uid".',
	        E_USER_ERROR
	    );
	    $uid = (int)$PHORUM['args']['uid'];
	    if($uid == $PHORUM['user']['user_id']) {
	    	$PHORUM['DATA']['ERROR']=$PHORUM["DATA"]["LANG"]["mod_block_user"]['NoBlockYourself'];
	    	$PHORUM['DATA']['SHOW_FORM']=0;
	    }
	    $user = phorum_api_user_get($uid);
    	$userposts = phorum_db_search('', $uid, false, 0, 1, 'USER_ID', 0, 'ALL');
    	$userpostcount = $userposts['count'];
	    
    	if($userpostcount > 100) {
    		$PHORUM["DATA"]['TOO_MANY']=1;
    	}
    	$PHORUM["DATA"]["LANG"]["mod_block_user"]['DeletePosts']=str_replace('%postcount%',$userpostcount,$PHORUM["DATA"]["LANG"]["mod_block_user"]['DeletePosts']);
    	$PHORUM["DATA"]["LANG"]["mod_block_user"]['Introduction']=str_replace('%display_name%',$user['display_name'],$PHORUM["DATA"]["LANG"]["mod_block_user"]['Introduction']);
    	
    	$PHORUM['DATA']['POST_VARS'].="<input type=\"hidden\" name=\"uid\" value=\"$uid\" />\n";
    	$PHORUM['DATA']['POST_VARS'].="<input type=\"hidden\" name=\"module\" value=\"block_user\" />\n";
    	// first call with user-id

    } else {
    	if(count($_POST)) {
    		
    		if(empty($_POST['uid'])) trigger_error(
		        'Illegal call to the block user mod addon handler: ' .
		        'missing post parameter "uid".',
		        E_USER_ERROR
		    	);
		    $uid = (int)$_POST['uid'];
		    
		    // Some safety precautions;
		    if(empty($uid)) {
		    	die('UID can\'t be empty!');
		    }
		    
		    if($PHORUM['user']['user_id'] == $uid) {
		    	die('You can\'t block yourself!');
		    }
		    
	    	$okmsgs=array();
	    	if(!empty($_POST['block_action'])) {
	    		$block_action = $_POST['block_action'];
	    		if(!empty($block_action)) {
		    		switch ($block_action) {
		    			case 'block_in_forum':
		    				phorum_db_mod_banlists(PHORUM_BAD_USERID, false, $uid, $PHORUM['forum_id'], "Banned by ".$PHORUM['user']['display_name']." at ".date('Y-m-d H:i:s'));
		    				$okmsgs[]=$PHORUM["DATA"]["LANG"]["mod_block_user"]['MsgBlockUserInForum'];
		    				break;
		  			
		    			case 'block_in_phorum':
		    				phorum_db_mod_banlists(PHORUM_BAD_USERID, false, $uid, 0, "Banned by ".$PHORUM['user']['display_name']." at ".date('Y-m-d H:i:s'));
		    				$okmsgs[]=$PHORUM["DATA"]["LANG"]["mod_block_user"]['MsgBlockUserInPhorum'];
		    				break;
		    			case 'deactivate_user':
		    				// deactivate user
		    				$user_update = array('user_id'=>$uid,'active'=>PHORUM_USER_INACTIVE);
		    				phorum_api_user_save($user_update);
		    				$okmsgs[]=$PHORUM["DATA"]["LANG"]["mod_block_user"]['MsgDeactivateUser'];
		    				break;
		    		}
	    		}
	    	}
	    	if(!empty($_POST['delete_posts'])) {
	    		$userposts = phorum_db_search('', $uid, false, 0, 100, 'USER_ID', 0, 'ALL');
	    		$message_ids = array_keys($userposts['rows']);
	    		$deleted=0;
	    		foreach($message_ids as $mid) {
	    			phorum_db_delete_message($mid,PHORUM_DELETE_MESSAGE);
	    			$deleted++;
	    		}
	    		$okmsgs[]=str_replace('%postcount%',$deleted,$PHORUM["DATA"]["LANG"]["mod_block_user"]['MsgDeletePosts']);
	    	}
	    	if(count($okmsgs)) {
	    		$PHORUM['DATA']['OKMSG']=implode("<br />\n", $okmsgs);
	    	}
    	}
    }
    phorum_output('block_user::addon');
}

function phorum_mod_block_user_profile($userdata) {
	global $PHORUM;
	
	$PHORUM['DATA']['URL']['BLOCK_USER']=phorum_get_url(PHORUM_ADDON_URL,'module=block_user','uid='.$userdata['user_id']);
	
	return $userdata;
}
// Display the impersonate user link on the profile page.
function phorum_mod_block_user_before_footer()
{
    $PHORUM = $GLOBALS["PHORUM"];

    if (phorum_page == 'profile') {
    	if(phorum_mod_block_user_check_allowed()) {
        	include(phorum_get_template('block_user::profile'));
    	}
    }
}

function phorum_mod_block_user_check_allowed() {
	global $PHORUM;
	
	$ret = false;
	if(!empty($PHORUM['user']['admin'])) {
	     $ret = true;
	}
	
	return $ret;
}


?>