<?php
chdir("../");
chdir("../");
include_once "./common.php";
$PHORUM = $GLOBALS["PHORUM"];
if (isset($_REQUEST["ECS_Access_Code"]) && $_SERVER["REMOTE_ADDR"] == $PHORUM["mod_easy_colorscheme"]["export_file_access_list"][$_REQUEST["ECS_Access_Code"]]["remote_server"]) {
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"".$_REQUEST["SchemeToExport"].".csf\"");
	$exportcolor_variables = $PHORUM["mod_easy_colorscheme"]["schemes"][$_REQUEST["SchemeToExport"]];
	$i = 0;
	foreach ($exportcolor_variables as $val) {
		if ($i == 0) {
			$exportsettingslist = $val["color_name"]."=".$val["color_value"];
		} else {
			$exportsettingslist = $exportsettingslist.",".$val["color_name"]."=".$val["color_value"];
		}
		$i = $i + 1;
	}
	print $exportsettingslist;
} else {
	print "File creation error!";
	exit;
}

?>
