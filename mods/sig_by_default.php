<?php
/* phorum module info
hook:  before_register|mod_sig_by_default_register
title: Signature by default
desc:  This module sets the "Add my signature to this message" flag for all newly registered users
category: user_management
*/

function mod_sig_by_default_register($udata) {
	$udata['show_signature']=1;
	return $udata;
}