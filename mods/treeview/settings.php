<?php

// Make sure that this script is loaded from the admin interface.
if(!defined("PHORUM_ADMIN")) return;

Global $PHORUM;

// Save settings in case this script is run after posting
// the settings form.
if(count($_POST)) 
{
    // Create the settings array for this module.
    $PHORUM["mod_treeview"]["line_color"] = isset($_POST["line_color"]) ? $_POST["line_color"] : "";
    $PHORUM["mod_treeview"]["line_width"] = isset($_POST["line_width"]) ? (int)$_POST["line_width"] : "";
    
    phorum_db_update_settings(array("mod_treeview"=>$PHORUM["mod_treeview"]));
    
    phorum_admin_okmsg("Settings updated");
}

$d = dir( "./templates" );
while ( false !== ( $entry = $d->read() ) ) {
	if ( $entry != "." && $entry != ".." && file_exists( "./templates/$entry/info.php" ) ) {
		include "./templates/$entry/info.php";
		if ( !isset( $template_hide ) || empty( $template_hide ) || defined( "PHORUM_ADMIN" ) ) {
			$all_templates[$entry]["template_name"] = $entry;
			$all_templates[$entry]["template_full_name"] = $name;
			$all_templates[$entry]["template_version"] = $version;
		} else {
			unset( $template_hide );
		}
	}
}

// We build the settings form by using the PhorumInputForm object. When
// creating your own settings screen, you'll only have to change the
// "mod" hidden parameter to the name of your own module.
include_once "./include/admin/PhorumInputForm.php";
$frm = new PhorumInputForm ("", "post", "Save");
$frm->hidden("module", "modsettings");
$frm->hidden("mod", "treeview"); 

// This adds a break line to your form, with a description on it.
// You can use this to separate your form into multiple sections.
$frm->addbreak("Edit settings for the Threaded Tree View module");
$frm->addrow("Color of the lines, leave blank to use each template's default color (eg. #000000 or Black): ", $frm->text_box("line_color", $PHORUM["mod_treeview"]["line_color"]));
$frm->addrow("Thickness (in pixels) of the lines, enter 0 to use each template's default thickness: ", $frm->text_box("line_width", $PHORUM["mod_treeview"]["line_width"], 5));  
// We are done building the settings screen.
// By calling show(), the screen will be displayed.
$frm->show();

?>