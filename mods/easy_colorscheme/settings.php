<?php
// Make sure that this script is loaded from the admin interface.
if(!defined("PHORUM_ADMIN")) return;

// Display the settings page (called at end of file)
function run_normal_page() {

$PHORUM = $GLOBALS["PHORUM"];

if (empty($PHORUM["admin_token"])) $PHORUM["admin_token"] = "";

//Very dangerous code to erase all settings.  Cannot be called without entering &erase_easy_colorscheme=VERITABLY into the address bar.
if (isset($_REQUEST["erase_easy_colorscheme"]) && $_REQUEST["erase_easy_colorscheme"] == "VERITABLY") {
	unset($PHORUM["mod_easy_colorscheme"]);
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
	Print "Settings Erased";
	return;
}
$pre_content = "";
if (isset($PHORUM["mod_easy_colorscheme"]["settings_log"])) {
	$pre_content = $PHORUM["mod_easy_colorscheme"]["settings_log"];
	unset($PHORUM["mod_easy_colorscheme"]["settings_log"]);
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
}
if (isset($PHORUM["mod_easy_colorscheme"]["schemes"])) {
	unset ($PHORUM["mod_easy_colorscheme"]["temp_settings"]);
	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
	$GLOBALS["ecs_debug"][] = "temp_settings unset";
}

if (isset($_POST["specifictemplate"])) {
	$template = $_POST["specifictemplate"];
} else {
	$template = basename( $PHORUM["default_forum_options"]["template"] );
}

$settingsfile = "./templates/".$template."/settings.tpl";

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
$GLOBALS["ecs_debug"][] = "template info collected";

$posscolornames = array (
	"AliceBlue" => "#F0F8FF",
	"AntiqueWhite" => "#FAEBD7",
	"Aqua" => "#00FFFF",
	"Aquamarine" => "#7FFFD4",
	"Azure" => "#F0FFFF",
	"Beige" => "#F5F5DC",
	"Bisque" => "#FFE4C4",
	"Black" => "#000000",
	"BlanchedAlmond" => "#FFEBCD",
	"Blue" => "#0000FF",
	"BlueViolet" => "#8A2BE2",
	"Brown" => "#A52A2A",
	"BurlyWood" => "#DEB887",
	"CadetBlue" => "#5F9EA0",
	"Chartreuse" => "#7FFF00",
	"Chocolate" => "#D2691E",
	"Coral" => "#FF7F50",
	"CornflowerBlue" => "#6495ED",
	"Cornsilk" => "#FFF8DC",
	"Crimson" => "#DC143C",
	"Cyan" => "#00FFFF",
	"DarkBlue" => "#00008B",
	"DarkCyan" => "#008B8B",
	"DarkGoldenRod" => "#B8860B",
	"DarkGray" => "#A9A9A9",
	"DarkGrey" => "#A9A9A9",
	"DarkGreen" => "#006400",
	"DarkKhaki" => "#BDB76B",
	"DarkMagenta" => "#8B008B",
	"DarkOliveGreen" => "#556B2F",
	"Darkorange" => "#FF8C00",
	"DarkOrchid" => "#9932CC",
	"DarkRed" => "#8B0000",
	"DarkSalmon" => "#E9967A",
	"DarkSeaGreen" => "#8FBC8F",
	"DarkSlateBlue" => "#483D8B",
	"DarkSlateGray" => "#2F4F4F",
	"DarkSlateGrey" => "#2F4F4F",
	"DarkTurquoise" => "#00CED1",
	"DarkViolet" => "#9400D3",
	"DeepPink" => "#FF1493",
	"DeepSkyBlue" => "#00BFFF",
	"DimGray" => "#696969",
	"DimGrey" => "#696969",
	"DodgerBlue" => "#1E90FF",
	"FireBrick" => "#B22222",
	"FloralWhite" => "#FFFAF0",
	"ForestGreen" => "#228B22",
	"Fuchsia" => "#FF00FF",
	"Gainsboro" => "#DCDCDC",
	"GhostWhite" => "#F8F8FF",
	"Gold" => "#FFD700",
	"GoldenRod" => "#DAA520",
	"Gray" => "#808080",
	"Grey" => "#808080",
	"Green" => "#008000",
	"GreenYellow" => "#ADFF2F",
	"HoneyDew" => "#F0FFF0",
	"HotPink" => "#FF69B4",
	"IndianRed " => "#CD5C5C",
	"Indigo " => "#4B0082",
	"Ivory" => "#FFFFF0",
	"Khaki" => "#F0E68C",
	"Lavender" => "#E6E6FA",
	"LavenderBlush" => "#FFF0F5",
	"LawnGreen" => "#7CFC00",
	"LemonChiffon" => "#FFFACD",
	"LightBlue" => "#ADD8E6",
	"LightCoral" => "#F08080",
	"LightCyan" => "#E0FFFF",
	"LightGoldenRodYellow" => "#FAFAD2",
	"LightGray" => "#D3D3D3",
	"LightGrey" => "#D3D3D3",
	"LightGreen" => "#90EE90",
	"LightPink" => "#FFB6C1",
	"LightSalmon" => "#FFA07A",
	"LightSeaGreen" => "#20B2AA",
	"LightSkyBlue" => "#87CEFA",
	"LightSlateGray" => "#778899",
	"LightSlateGrey" => "#778899",
	"LightSteelBlue" => "#B0C4DE",
	"LightYellow" => "#FFFFE0",
	"Lime" => "#00FF00",
	"LimeGreen" => "#32CD32",
	"Linen" => "#FAF0E6",
	"Magenta" => "#FF00FF",
	"Maroon" => "#800000",
	"MediumAquaMarine" => "#66CDAA",
	"MediumBlue" => "#0000CD",
	"MediumOrchid" => "#BA55D3",
	"MediumPurple" => "#9370D8",
	"MediumSeaGreen" => "#3CB371",
	"MediumSlateBlue" => "#7B68EE",
	"MediumSpringGreen" => "#00FA9A",
	"MediumTurquoise" => "#48D1CC",
	"MediumVioletRed" => "#C71585",
	"MidnightBlue" => "#191970",
	"MintCream" => "#F5FFFA",
	"MistyRose" => "#FFE4E1",
	"Moccasin" => "#FFE4B5",
	"NavajoWhite" => "#FFDEAD",
	"Navy" => "#000080",
	"OldLace" => "#FDF5E6",
	"Olive" => "#808000",
	"OliveDrab" => "#6B8E23",
	"Orange" => "#FFA500",
	"OrangeRed" => "#FF4500",
	"Orchid" => "#DA70D6",
	"PaleGoldenRod" => "#EEE8AA",
	"PaleGreen" => "#98FB98",
	"PaleTurquoise" => "#AFEEEE",
	"PaleVioletRed" => "#D87093",
	"PapayaWhip" => "#FFEFD5",
	"PeachPuff" => "#FFDAB9",
	"Peru" => "#CD853F",
	"Pink" => "#FFC0CB",
	"Plum" => "#DDA0DD",
	"PowderBlue" => "#B0E0E6",
	"Purple" => "#800080",
	"Red" => "#FF0000",
	"RosyBrown" => "#BC8F8F",
	"RoyalBlue" => "#4169E1",
	"SaddleBrown" => "#8B4513",
	"Salmon" => "#FA8072",
	"SandyBrown" => "#F4A460",
	"SeaGreen" => "#2E8B57",
	"SeaShell" => "#FFF5EE",
	"Sienna" => "#A0522D",
	"Silver" => "#C0C0C0",
	"SkyBlue" => "#87CEEB",
	"SlateBlue" => "#6A5ACD",
	"SlateGray" => "#708090",
	"SlateGrey" => "#708090",
	"Snow" => "#FFFAFA",
	"SpringGreen" => "#00FF7F",
	"SteelBlue" => "#4682B4",
	"Tan" => "#D2B48C",
	"Teal" => "#008080",
	"Thistle" => "#D8BFD8",
	"Tomato" => "#FF6347",
	"Turquoise" => "#40E0D0",
	"Violet" => "#EE82EE",
	"Wheat" => "#F5DEB3",
	"White" => "#FFFFFF",
	"WhiteSmoke" => "#F5F5F5",
	"Yellow" => "#FFFF00",
	"YellowGreen" => "#9ACD32"
	);

// Get the current forum color scheme
if ($PHORUM["mod_easy_colorscheme"]["usescheme"]) {
	$currforumscheme = $PHORUM["mod_easy_colorscheme"]["usescheme"];
} else {
	$currforumscheme = "DefaultColors";
}
$currglobalscheme = $currforumscheme;
$GLOBALS["ecs_debug"][] = "current global scheme set";

if (isset($_REQUEST["pullscheme"]) && $_REQUEST["pullscheme"] == "fromtpl") {
	if (isset($_POST["specifictemplate"])) {
		$currforumscheme = str_replace(" ","_",$all_templates[$_POST["specifictemplate"]]["template_full_name"]."_".$all_templates[$_POST["specifictemplate"]]["template_version"]."_Default_Colors");
	}
	$osf = fopen($settingsfile, "r");
	$osf_contents = fread($osf, filesize($settingsfile));
	fclose($osf);
	
	preg_match_all("/\{VAR\s+(.+?)\s+\"(.+?)\"\}/", $osf_contents, $osf_array);
	
	$i=0;
	foreach ($osf_array[2] as $osf_3 => $osf_4) {
		if (preg_match("/\#[\dABCDEF][\dABCDEF][\dABCDEF][\dABCDEF][\dABCDEF][\dABCDEF]/i",$osf_4,$osf_blank)) {
			$color_variables[$i]["color_name"] = $osf_array[1][$osf_3];
			$color_variables[$i]["color_value"] = strtoupper($osf_4);
			$i++;
		} else {
			foreach ($posscolornames as $pcolor_name => $pcolor_value) {
				if (strtolower($osf_4) == strtolower($pcolor_name)) {
					$color_variables[$i]["color_name"] = $osf_array[1][$osf_3];
					$color_variables[$i]["color_value"] = $pcolor_value;
					$i++;
				}
			}
			if (strtolower($osf_4) == "templates/emerald/images/top_background.png" && function_exists(imagecreatefrompng)) {
				$image_in = imagecreatefrompng("./".$osf_4);
				$imageinfo = getimagesize("./".$osf_4);
				$width = $imageinfo[0];
				$height = $imageinfo[1];
				$pixelrgb = imagecolorat($image_in,0,0);
				$cols = imagecolorsforindex($image_in, $pixelrgb);
				$color_variables[$i]["color_name"] = "top_image_upper_color";
				$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["img_width"] = $width;
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["img_height"] = $height;
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["var"] = $osf_array[1][$osf_3];
				$i++;				
				$pixelrgb = imagecolorat($image_in,0,$height-2);
				$cols = imagecolorsforindex($image_in, $pixelrgb);
				$color_variables[$i]["color_name"] = "top_image_lower_color";
				$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
				$i++;
			}
			if (strtolower($osf_4) == "templates/emerald/images/header_background.png" && function_exists(imagecreatefrompng)) {
				$image_in = imagecreatefrompng("./".$osf_4);
				$imageinfo = getimagesize("./".$osf_4);
				$width = $imageinfo[0];
				$height = $imageinfo[1];
				$pixelrgb = imagecolorat($image_in,0,0);
				$cols = imagecolorsforindex($image_in, $pixelrgb);
				$color_variables[$i]["color_name"] = "header_image_upper_color";
				$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["img_width"] = $width;
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["img_height"] = $height;
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["var"] = $osf_array[1][$osf_3];
				$i++;				
				$pixelrgb = imagecolorat($image_in,0,$height-1);
				$cols = imagecolorsforindex($image_in, $pixelrgb);
				$color_variables[$i]["color_name"] = "header_image_lower_color";
				$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
				$i++;
			}
			if (strtolower($osf_4) == "templates/emerald/images/message_background.png" && function_exists(imagecreatefrompng)) {
				$image_in = imagecreatefrompng("./".$osf_4);
				$imageinfo = getimagesize("./".$osf_4);
				$width = $imageinfo[0];
				$height = $imageinfo[1];
				$pixelrgb = imagecolorat($image_in,0,0);
				$cols = imagecolorsforindex($image_in, $pixelrgb);
				$color_variables[$i]["color_name"] = "message_image_upper_color";
				$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["img_width"] = $width;
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["img_height"] = $height;
				$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["var"] = $osf_array[1][$osf_3];
				$i++;				
				$pixelrgb = imagecolorat($image_in,0,$height-1);
				$cols = imagecolorsforindex($image_in, $pixelrgb);
				$color_variables[$i]["color_name"] = "message_image_lower_color";
				$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
				$i++;
			}
		}
	}

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));	
	$GLOBALS["ecs_debug"][] = "scheme pulled from template";
} else if (isset($_REQUEST["viewscheme"]) && $_REQUEST["viewscheme"]) {
	$color_variables = $GLOBALS["PHORUM"]["mod_easy_colorscheme"]["schemes"][$_REQUEST["viewscheme"]];
	$GLOBALS["ecs_debug"][] = "color variables pulled from selected scheme";
} else {
	if (!isset($PHORUM["mod_easy_colorscheme"]["schemes"])) {
		$GLOBALS["ecs_debug"][] = "no schemes detected";
		$osf = fopen($settingsfile, "r");
		$osf_contents = fread($osf, filesize($settingsfile));
		fclose($osf);
	
		preg_match_all("/\{VAR\s+(.+?)\s+\"(.+?)\"\}/", $osf_contents, $osf_array);
	
		$i=0;
		foreach ($osf_array[2] as $osf_3 => $osf_4) {
			if (preg_match("/\#[\dABCDEF][\dABCDEF][\dABCDEF][\dABCDEF][\dABCDEF][\dABCDEF]/i",$osf_4,$osf_blank)) {
				$color_variables[$i]["color_name"] = $osf_array[1][$osf_3];
				$color_variables[$i]["color_value"] = strtoupper($osf_4);
				$i++;
			} else {
				foreach ($posscolornames as $pcolor_name => $pcolor_value) {
					if (strtolower($osf_4) == strtolower($pcolor_name)) {
						$color_variables[$i]["color_name"] = $osf_array[1][$osf_3];
						$color_variables[$i]["color_value"] = $pcolor_value;
						$i++;
					}
				}
				if (strtolower($osf_4) == "templates/emerald/images/top_background.png" && function_exists(imagecreatefrompng)) {
					$image_in = imagecreatefrompng("./".$osf_4);
					$imageinfo = getimagesize("./".$osf_4);
					$width = $imageinfo[0];
					$height = $imageinfo[1];
					$pixelrgb = imagecolorat($image_in,0,0);
					$cols = imagecolorsforindex($image_in, $pixelrgb);
					$color_variables[$i]["color_name"] = "top_image_upper_color";
					$color_variables[$i]["color_value"] = "#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]);
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["top_image"]["img_width"] = $width;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["top_image"]["img_height"] = $height;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["top_image"]["var"] = $osf_array[1][$osf_3];
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_width"] = $width;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_height"] = $height;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["var"] = $osf_array[1][$osf_3];
					$i++;					
					$pixelrgb = imagecolorat($image_in,0,$height-2);
					$cols = imagecolorsforindex($image_in, $pixelrgb);
					$color_variables[$i]["color_name"] = "top_image_lower_color";
					$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
					$i++;
				}
				if (strtolower($osf_4) == "templates/emerald/images/header_background.png" && function_exists(imagecreatefrompng)) {
					$image_in = imagecreatefrompng("./".$osf_4);
					$imageinfo = getimagesize("./".$osf_4);
					$width = $imageinfo[0];
					$height = $imageinfo[1];
					$pixelrgb = imagecolorat($image_in,0,0);
					$cols = imagecolorsforindex($image_in, $pixelrgb);
					$color_variables[$i]["color_name"] = "header_image_upper_color";
					$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["header_image"]["img_width"] = $width;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["header_image"]["img_height"] = $height;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["header_image"]["var"] = $osf_array[1][$osf_3];
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["img_width"] = $width;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["img_height"] = $height;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["var"] = $osf_array[1][$osf_3];
					$i++;
					$pixelrgb = imagecolorat($image_in,0,$height-1);
					$cols = imagecolorsforindex($image_in, $pixelrgb);
					$color_variables[$i]["color_name"] = "header_image_lower_color";
					$color_variables[$i]["color_value"] = "#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]);
					$i++;
				}
				if (strtolower($osf_4) == "templates/emerald/images/message_background.png" && function_exists(imagecreatefrompng)) {
					$image_in = imagecreatefrompng("./".$osf_4);
					$imageinfo = getimagesize("./".$osf_4);
					$width = $imageinfo[0];
					$height = $imageinfo[1];
					$pixelrgb = imagecolorat($image_in,0,0);
					$cols = imagecolorsforindex($image_in, $pixelrgb);
					$color_variables[$i]["color_name"] = "message_image_upper_color";
					$color_variables[$i]["color_value"] = strtoupper("#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]));
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["message_image"]["img_width"] = $width;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["message_image"]["img_height"] = $height;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["DefaultColors"]["image_info"]["message_image"]["var"] = $osf_array[1][$osf_3];
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["img_width"] = $width;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["img_height"] = $height;
					$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["var"] = $osf_array[1][$osf_3];
					$i++;					
					$pixelrgb = imagecolorat($image_in,0,$height-1);
					$cols = imagecolorsforindex($image_in, $pixelrgb);
					$color_variables[$i]["color_name"] = "message_image_lower_color";
					$color_variables[$i]["color_value"] = "#".dechex($cols["red"]).dechex($cols["green"]).dechex($cols["blue"]);
					$i++;
				}
			}	
		}

		$PHORUM["mod_easy_colorscheme"]["schemes"]["DefaultColors"] = $color_variables;

		$PHORUM['mod_easy_colorscheme']['css_version'] =
			isset($PHORUM['mod_easy_colorscheme']['css_version'])
			? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
		phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
		$GLOBALS["ecs_debug"][] = "DefaultColors scheme created";
	} else {
		$color_variables = $PHORUM["mod_easy_colorscheme"]["schemes"][$currforumscheme];
		$GLOBALS["ecs_debug"][] = "color variables pulled from global scheme";
	}
}

//define default image sizes and variables if undefined
if (!isset($PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_width"])) {
	$GLOBALS["ecs_debug"][] = "no default image info detected";
//define default top_image size and variable
	if (file_exists( "./templates/emerald/images/top_background.png" && function_exists(imagecreatefrompng))) {
		$image_in = imagecreatefrompng("./templates/emerald/images/top_background.png");
		$imageinfo = getimagesize("./templates/emerald/images/top_background.png");
		$width = $imageinfo[0];
		$height = $imageinfo[1];
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_width"] = $width;
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_height"] = $height;
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["var"] = "top_background_image";
	}
//define default header_image size and variable
	if (file_exists( "./templates/emerald/images/header_background.png" && function_exists(imagecreatefrompng) )) {
		$image_in = imagecreatefrompng("./templates/emerald/images/header_background.png");
		$imageinfo = getimagesize("./templates/emerald/images/header_background.png");
		$width = $imageinfo[0];
		$height = $imageinfo[1];
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["img_width"] = $width;
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["img_height"] = $height;
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["var"] = "header_background_image";
	}
//define default message_image size and variable
	if (file_exists( "./templates/emerald/images/message_background.png" && function_exists(imagecreatefrompng) )) {	
		$image_in = imagecreatefrompng("./templates/emerald/images/message_background.png");
		$imageinfo = getimagesize("./templates/emerald/images/message_background.png");
		$width = $imageinfo[0];
		$height = $imageinfo[1];
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["img_width"] = $width;
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["img_height"] = $height;
		$PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["var"] = "message_background_image";
	}
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
	$GLOBALS["ecs_debug"][] = "default image info set";
}
		
$i = 1;
if (isset($_REQUEST["viewscheme"])) {
	$currviewscheme = $_REQUEST["viewscheme"];
	$GLOBALS["ecs_debug"][] = "current scheme pulled from selection";
} else {
	$currviewscheme = $currforumscheme;
	$GLOBALS["ecs_debug"][] = "current scheme pulled from global scheme";
}
if (!isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"])) {
	$GLOBALS["ecs_debug"][] = "no temporary image info defined";
	if (isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$currviewscheme]["image_info"])) {
		$PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"] = $PHORUM["mod_easy_colorscheme"]["schemes_images"][$currviewscheme]["image_info"];
		$GLOBALS["ecs_debug"][] = "temporary image info pulled from current scheme";
	}
	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
}
//generate alphabetical list of schemes
ksort($PHORUM["mod_easy_colorscheme"]["schemes"]);
//print_r($PHORUM["mod_easy_colorscheme"]["schemes"]);
//exit();

$pre_content .=  "<table border='0' cellspacing='2' cellpadding='2' class='input-form-table' width='100%' id='maintable'>
<tr class='input-form-tr'>
  <td class='input-form-td-break'>Easy Color Scheme Manager Settings</td></tr>
  <tr class='input-form-tr'><th align='left' class='input-form-th'>Current Global Forum Color Scheme: ".$currglobalscheme."</th></tr>
  <tr class='input-form-tr'><td class='input-form-td-break'>\"".$currviewscheme."\" color scheme settings:</td></tr>
  <tr><td>";
$GLOBALS["ecs_debug"][] = "pre_content established";
// Import html color table and javascript functions
// include_once ( "colortable.modx" );
// Now called at end of function

$content ="</td></tr><tr class='input-form-tr'><th align='left' class='input-form-th'>Select a variable below to edit then click a color above or enter your own hexadecimal code.";

if (!isset($PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_width"])) {
	$content .="<br><font color=red>You will not be able to create background images properly as there were no default images from which to establish dimensions.</font>";
	$GLOBALS["ecs_debug"][] = "default image info unavailable";
}

$content .="</td></tr>
	<tr><td><table border=0><tr><td valign=top><table border=1 bordercolor=#000000 cellspacing=0><tr><td><b>&nbsp;</b></td><td><b>Variable</b></td><td><b>Color</b></td><td>&nbsp;&nbsp;&nbsp;</td><td><b>Undo</b></td></tr>
	<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' onSubmit=\"return confirmaction('Save')\" method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />";
$i = 1;
$row = 8;
if (count($color_variables) > 24) {
	$row = intval(count($color_variables)/3);
}
foreach ($color_variables as $val) {
	if (isset($val["color_name"])){
		$content .="<tr>
		<td align=center><input type='radio' name='colorvariable' onclick='changecurrchoice(this.value)' id='".$val["color_name"]."radio' value='".$val["color_name"]."'></td>
		<td>".$val["color_name"]."</td>
		<td><input type='text' size='7' onfocus='changecurrchoice(this.name)' onchange='manualchange(this.value)' id='".$val["color_name"]."text' name='".$val["color_name"]."' value='".$val["color_value"]."'></td>
		<td bgColor='".$val["color_value"]."' id='".$val["color_name"]."demo'>&nbsp;</td>
		<td align=center><input type='radio' name='".$val["color_name"]."' id='".$val["color_name"]."undo' disabled='true' onclick='undochange(this.name,this.value,this)'></td>
		</tr>";
		if ($i == $row || $i == ($row * 2)) {
			$content .="</table></td><td valign=top><table border=1 bordercolor=#000000 cellspacing=0><tr><td><b>&nbsp;</b></td><td><b>Variable</b></td><td><b>Color</b></td><td>&nbsp;&nbsp;&nbsp;</td><td><b>Undo</b></td></tr>";
		}
		$i = $i + 1;
	}
}
$GLOBALS["ecs_debug"][] = "color variables added to color selection table";	
$content .="</table></td></tr></table>";//<a href='./admin.php?module=modsettings&mod=easy_colorscheme&pullscheme=fromtpl'>Get settings from settings.tpl</a><br>";
$GLOBALS["ecs_debug"][] = "color selection table added to content";
// Save colors as a scheme
$content .="</td></tr><tr class='input-form-tr'><th align='left' class='input-form-th'>Save current color scheme as: <input type='text' id='saveschemename' name='schemename' size='35' value='";
if (!$currviewscheme) {
	$content .="DefaultColors";
} else {
	$content .=$currviewscheme;
}
$content .= "'><input type='hidden' name='ECS_MODE' value='Save'><input type='hidden' name='color_variables' value='";
$color_variables_str = "";
foreach ($color_variables as $key => $value) {
	foreach ($value as $subkey => $subvalue) {
		$color_variables_str .= "$subvalue,";
	}
	$color_variables_str .= ";";
}
$content .= "$color_variables_str'>";
$content .= "&nbsp;&nbsp;<input type='submit' value='Save' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "save colors as scheme table added to content";

// Export color settings for clipboard
$content .= "<form><tr class='input-form-tr'><th align='left' class='input-form-th'>Create a list of settings from the values above: <input type='button' id='SettingListButton' value='Generate Setting List' class='input-form-submit' onclick='showsettingslist()'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "export color settings dialog added to content";

//Get colors from specific template
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."&pullscheme=fromtpl' method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' /><tr class='input-form-tr'><th align='left' class='input-form-th'>
	Get default settings from a specific template: <select name='specifictemplate'>";
foreach ($all_templates as $specifictemplate) {
	$content .= "<option value='".$specifictemplate["template_name"]."'>".$specifictemplate["template_full_name"]." ".$specifictemplate["template_version"]."</option>";
}
$content .= "</select> <input type='submit' value='Get Settings' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "get colors from template dialog added to content";

// Apply a saved scheme to all forums
$content .= "<tr class='input-form-tr'><td class='input-form-td-break'>Apply Saved Color Schemes:</td></tr>
	<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' onSubmit=\"return confirmaction('Apply')\" method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<input type='hidden' name='ECS_MODE' value='ApplyAll'>
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	Apply a color scheme to all forums: <select id='applyschemeall' name='applyscheme'>";
foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
	$content .= "<option value='".$key."'>".$key."</option>";
}
$content .= "</select> <input type='submit' value='Apply to All' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "apply scheme globally dialog added to content";

// Apply saved schemes to specific templates
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<input type='hidden' name='ECS_MODE' value='ToggleSpecificTemplate'>
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	Apply saved schemes to specific templates (overrides global color scheme): <select onchange='this.form.submit()' name='applyspecifictemplatetoggle' id='applyspecifictemplatetoggle'>
	<option value='0'>Off</option><option value='1'";
	if (!empty($PHORUM["mod_easy_colorscheme"]["useschemesbytemplate"])) {
		$content .= " selected='true'";
	}
	$content .= ">On</option></select></th></tr></form>";
if (!empty($PHORUM["mod_easy_colorscheme"]["useschemesbytemplate"])) {
	foreach ($all_templates as $specifictemplate) {
		$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' onSubmit=\"return confirmaction('ApplySpecificTemplate".$specifictemplate["template_name"]."')\" method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
			<input type='hidden' name='ECS_MODE' value='ApplySpecificTemplate'>
			<input type='hidden' name='applytotemplate' value='".$specifictemplate["template_name"]."'>
			<input type='hidden' id='specifictemplatename".$specifictemplate["template_name"]."' value=\"".$specifictemplate["template_full_name"]." ".$specifictemplate["template_version"]."\">
			<tr class='input-form-tr'><th align='left' class='input-form-th'>
			<select id='applyschemespecific".$specifictemplate["template_name"]."' name='applyscheme'>";
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
			$content .= "<option value='".$key."'";
			if (isset($PHORUM["mod_easy_colorscheme"]["templateschemes"][$specifictemplate["template_name"]]) && $key == $PHORUM["mod_easy_colorscheme"]["templateschemes"][$specifictemplate["template_name"]]) {
				$content .= " selected='true'";
			}
			$content .= ">".$key."</option>";
		}
		$content .= "</select> <input type='submit' value=\"Apply to ".$specifictemplate["template_full_name"]." ".$specifictemplate["template_version"]."\" class='input-form-submit'></th></tr></form>";
	}
	$GLOBALS["ecs_debug"][] = "templates with applied schemes added to content";
}
$GLOBALS["ecs_debug"][] = "apply scheme to templates dialog added to content";

// Apply saved schemes to specific forums
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<input type='hidden' name='ECS_MODE' value='ToggleSpecific'>
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	Apply saved schemes to specific forums (overrides global and template-specific color schemes): <select onchange='this.form.submit()' name='applyspecifictoggle' id='applyspecifictoggle'>
	<option value='0'>Off</option><option value='1'";
	if (isset($PHORUM["mod_easy_colorscheme"]["useschemesbyforum"]) && $PHORUM["mod_easy_colorscheme"]["useschemesbyforum"]) {
		$content .= " selected='true'";
	}
	$content .= ">On</option></select></th></tr></form>";
if (isset($PHORUM["mod_easy_colorscheme"]["useschemesbyforum"]) && $PHORUM["mod_easy_colorscheme"]["useschemesbyforum"]) {
	$all_forums = phorum_db_get_forums( 0, 0);
	foreach ($all_forums as $specificforum) {
		$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' onSubmit=\"return confirmaction('ApplySpecific".$specificforum["forum_id"]."')\" method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
			<input type='hidden' name='ECS_MODE' value='ApplySpecific'>
			<input type='hidden' name='applytoforum' value='".$specificforum["forum_id"]."'>
			<input type='hidden' id='specificforumname".$specificforum["forum_id"]."' value=\"".$specificforum["name"]."\">
			<tr class='input-form-tr'><th align='left' class='input-form-th'>
			<select id='applyschemespecific".$specificforum["forum_id"]."' name='applyscheme'>";
			foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
				$content .= "<option value='".$key."'";
				if (isset($PHORUM["mod_easy_colorscheme"]["forumschemes"][$specificforum["forum_id"]]) && $key == $PHORUM["mod_easy_colorscheme"]["forumschemes"][$specificforum["forum_id"]]) {
					$content .= " selected='true'";
				}
				$content .= ">".$key."</option>";
			}
		$content .= "</select> <input type='submit' value=\"Apply to ".$specificforum["name"]."\" class='input-form-submit'></th></tr></form>";
	}
	$GLOBALS["ecs_debug"][] = "forums with applied schemes added to content";
}
$GLOBALS["ecs_debug"][] = "apply scheme to forums dialog added to content";

$content .= "<tr class='input-form-tr'><td class='input-form-td-break'>Manage Saved Color Schemes:</td></tr>";

//View saved schemes
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' /><tr class='input-form-tr'><th align='left' class='input-form-th'>
	View/edit saved color scheme: <select name='viewscheme'>";
foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
	$content .= "<option value='".$key."'>".$key."</option>";
}
$content .= "</select> <input type='submit' value='View' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "view saved schemes dialog added to content";

// Delete saved schemes
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' onSubmit=\"return confirmaction('Delete')\" method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	<input type='hidden' name='ECS_MODE' value='Delete'>
	Delete a color scheme: <select name='deletescheme' id='deletescheme'>";
foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
	$content .= "<option value='".$key."'>".$key."</option>";
}
$content .= "</select> <input type='submit' value='Delete' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "delete saved schemes dialog added to content";

// Preview forum with selected scheme
$content .= "<form><tr class='input-form-tr'><th align='left' class='input-form-th'>Open new window with a live preview of a color scheme: <select id='previewscheme' name='viewscheme'>";
foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
	$content .= "<option value='".$key."'>".$key."</option>";
}
$content .= "</select> <input type='button' value='Preview' class='input-form-submit' onclick='showpreview()'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "preview selected scheme dialog added to content";

$content .= "<tr class='input-form-tr'><td class='input-form-td-break'>Export Saved Color Schemes:</td></tr>";
// Export color scheme to a file.
if (isset($PHORUM["mod_easy_colorscheme"]["export_file_access_list"])) {
	foreach ($PHORUM["mod_easy_colorscheme"]["export_file_access_list"] as $set_access_code => $vals) {
		if ($vals["created_time"] + 1200 < time()) {
			unset($PHORUM["mod_easy_colorscheme"]["export_file_access_list"][$set_access_code]);
		}
	}
}
srand((double)microtime()*1000000);  
$randnum = rand(0,100);
$created_time = time();
$generated_code["created_time"] = $created_time;
$generated_code["remote_server"] = $_SERVER["REMOTE_ADDR"];
$access_code = $created_time.$randnum;
$PHORUM["mod_easy_colorscheme"]["export_file_access_list"][$access_code] = $generated_code;
phorum_db_update_settings(array("mod_easy_colorscheme"=>$PHORUM["mod_easy_colorscheme"]));
$content .= "<tr class='input-form-tr'><th align='left' class='input-form-th'>
	Export a list of settings to a csf file from: <select id='exportfileschemename' name='exportfileschemename'>";
foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
	$content .= "<option value='".$key."'>".$key."</option>";
}
$content .= "</select> <input type='button' value='Export Setting List' class='input-form-submit' onclick=\"exportschemetofile('".$access_code."')\"></th></tr></form>";
$GLOBALS["ecs_debug"][] = "export scheme to file dialog added to content";

// Export color scheme for clipboard
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	<input type='hidden' name='ECS_MODE' value='ExportList'>
	Create a list of settings from: <select id='exportschemename' name='exportschemename'>";
foreach ($PHORUM["mod_easy_colorscheme"]["schemes"] as $key => $val) {
	$content .= "<option value='".$key."'>".$key."</option>";
}
$content .= "</select> <input type='submit' value='Generate Setting List' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "export scheme to clipboard dialog added to content";

// Show color scheme settings for copying
if (isset($_REQUEST["ECS_MODE"]) && $_REQUEST["ECS_MODE"] == "ExportList") {
	$exportschemename = $_REQUEST["exportschemename"];
	$exportcolor_variables = $PHORUM["mod_easy_colorscheme"]["schemes"][$exportschemename];
	$i = 0;
	foreach ($exportcolor_variables as $val) {
		if ($i == 0) {
			$exportsettingslist = $val["color_name"]."=".$val["color_value"];
		} else {
			$exportsettingslist = $exportsettingslist.",".$val["color_name"]."=".$val["color_value"];
		}
		$i = $i + 1;
	}
	$content .= "<tr><td>
	\"".$exportschemename."\" color scheme settings: <input type=text id='exportsavedsettingslist' name='exportsavedsettingslist' readonly='true' size='50' value='".$exportsettingslist."'><br>
	<i>Copy this list of settings to share via forums/email.</i></td></tr>
	<script>document.getElementById('exportsavedsettingslist').focus();
	document.getElementById('exportsavedsettingslist').select();</script>";
}
$GLOBALS["ecs_debug"][] = "show scheme for copying dialog added to content";

$content .= "<tr class='input-form-tr'><td class='input-form-td-break'>Import Color Schemes:</td></tr>";

// Import color scheme from a file
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' id='importfileform' onSubmit='return validateimportfile(this)' method='post' enctype='multipart/form-data'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	<input type='hidden' name='ECS_MODE' value='ImportFile'>
	Import a color scheme from a csf file: <input type='file' name='importfilename' size='35' onchange='importfileadded(this)' /><br />
	to this color scheme: <input type='text' name='importschemename' size='35' /> 
	<input type='submit' value='Import Setting List' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "import scheme from file dialog added to content";

// Import color scheme from pasted list
$content .= "<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' onSubmit='return validateimportlist(this)' method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	<input type='hidden' name='ECS_MODE' value='Save'>
	Import this list of settings: <input type='text' name='pastedimportsettings' size='20' value=''> 
	to this color scheme: <input type='text' name='schemename' size='35' value=''> 
	<input type='submit' value='Import Setting List' class='input-form-submit'></th></tr></form>";
$GLOBALS["ecs_debug"][] = "import scheme from clipboard dialog added to content";

// Enable/disable debugging
if (isset($_REQUEST["enable_debugging"]) || !empty($PHORUM["mod_easy_colorscheme"]["debugtoggle"])) {
$content .= "<tr class='input-form-tr'><td class='input-form-td-break'>Debugging:</td></tr>
	<form style='display: inline;' action='./admin.php?module=modsettings&mod=easy_colorscheme&phorum_admin_token=".$PHORUM["admin_token"]."' method='post'>
  <input type='hidden' name='phorum_admin_token' value='".$PHORUM["admin_token"]."' />
	<input type='hidden' name='ECS_MODE' value='ToggleDebugging'>
	<tr class='input-form-tr'><th align='left' class='input-form-th'>
	Debug this module: <select onchange='this.form.submit()' name='debugtoggle' id='debugtoggle'>
	<option value='0'>Off</option><option value='1'";
	if (!empty($PHORUM["mod_easy_colorscheme"]["debugtoggle"])) {
		$content .= " selected='true'";
	}
$content .= ">On</option></select></th></tr></form>";
}

// Acknowledge the work done by colorDavid
$content .= "<tr><td>
	<br><font size='1'>Hexagonal Color Table by <a target='_blank' href='http://www.stokhorst.nl/colorDavid/'>colorDavid</a>. Include this link with any use.</font>
	</td></tr></table>";

print $pre_content;
// Import html color table and javascript functions
include_once ( "colortable.modx" );
print $content;

}


// Save color settings or imported settings list as a scheme then reload the settings page 
// Called by ECS_MODE = Save

function run_save_page() {

$PHORUM = $GLOBALS["PHORUM"];
		
if (isset($_REQUEST["pastedimportsettings"])) {
	$settings_log_title = "Import settings from pasted list";
	$i=0;
	$rawimportlist = $_REQUEST["pastedimportsettings"];
	$semirawimportlist = explode(",",$rawimportlist);
	foreach ($semirawimportlist as $key) {
		$rawvariables = explode("=",$key);
		$color_variables[$i]["color_name"] = $rawvariables[0];
		$color_variables[$i]["color_value"] = $rawvariables[1];
		$_REQUEST[$rawvariables[0]] = $rawvariables[1];
		$i++;
	}
} else if (isset($_REQUEST["ECS_MODE"]) && $_REQUEST["ECS_MODE"] == "ImportFile") {
	if (!isset($_FILES["importfilename"]) || substr($_FILES["importfilename"]["name"],-4) != ".csf" || $_FILES["importfilename"]["type"] != ("text/plain" || "application/octet-stream") || $_FILES["importfilename"]["size"] > 3000) {
		$PHORUM["mod_easy_colorscheme"]["settings_log"] = "<b style='color:#FF0000'>Import settings from file failed: illegal file type or no file specified.</b>";
		phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
    $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
		header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token);
		return;
	}
	$settings_log_title = "Import settings from file";
	$i=0;
	$rawimportlist = file_get_contents($_FILES["importfilename"]["tmp_name"]);
	$semirawimportlist = explode(",",$rawimportlist);
	foreach ($semirawimportlist as $key) {
		$rawvariables = explode("=",$key);
		$color_variables[$i]["color_name"] = $rawvariables[0];
		$color_variables[$i]["color_value"] = $rawvariables[1];
		$_REQUEST[$rawvariables[0]] = $rawvariables[1];
		$i++;
	$schemename = $_REQUEST["importschemename"];
	}	
} else {
	$settings_log_title = "Save settings";
	$i=0;
	$var_temp = explode(";",$_POST["color_variables"]);
	foreach ($var_temp as $value) {
		$vars = explode(",",$value);
		if (isset($vars[0])) $color_variables[$i]["color_name"] = $vars[0];
		if (isset($vars[1])) $color_variables[$i]["color_value"] = $vars[1];
		$i++;
	}
	unset($color_variables[$i-1]);
}

$posscolornames = array (
	"AliceBlue" => "#F0F8FF",
	"AntiqueWhite" => "#FAEBD7",
	"Aqua" => "#00FFFF",
	"Aquamarine" => "#7FFFD4",
	"Azure" => "#F0FFFF",
	"Beige" => "#F5F5DC",
	"Bisque" => "#FFE4C4",
	"Black" => "#000000",
	"BlanchedAlmond" => "#FFEBCD",
	"Blue" => "#0000FF",
	"BlueViolet" => "#8A2BE2",
	"Brown" => "#A52A2A",
	"BurlyWood" => "#DEB887",
	"CadetBlue" => "#5F9EA0",
	"Chartreuse" => "#7FFF00",
	"Chocolate" => "#D2691E",
	"Coral" => "#FF7F50",
	"CornflowerBlue" => "#6495ED",
	"Cornsilk" => "#FFF8DC",
	"Crimson" => "#DC143C",
	"Cyan" => "#00FFFF",
	"DarkBlue" => "#00008B",
	"DarkCyan" => "#008B8B",
	"DarkGoldenRod" => "#B8860B",
	"DarkGray" => "#A9A9A9",
	"DarkGrey" => "#A9A9A9",
	"DarkGreen" => "#006400",
	"DarkKhaki" => "#BDB76B",
	"DarkMagenta" => "#8B008B",
	"DarkOliveGreen" => "#556B2F",
	"Darkorange" => "#FF8C00",
	"DarkOrchid" => "#9932CC",
	"DarkRed" => "#8B0000",
	"DarkSalmon" => "#E9967A",
	"DarkSeaGreen" => "#8FBC8F",
	"DarkSlateBlue" => "#483D8B",
	"DarkSlateGray" => "#2F4F4F",
	"DarkSlateGrey" => "#2F4F4F",
	"DarkTurquoise" => "#00CED1",
	"DarkViolet" => "#9400D3",
	"DeepPink" => "#FF1493",
	"DeepSkyBlue" => "#00BFFF",
	"DimGray" => "#696969",
	"DimGrey" => "#696969",
	"DodgerBlue" => "#1E90FF",
	"FireBrick" => "#B22222",
	"FloralWhite" => "#FFFAF0",
	"ForestGreen" => "#228B22",
	"Fuchsia" => "#FF00FF",
	"Gainsboro" => "#DCDCDC",
	"GhostWhite" => "#F8F8FF",
	"Gold" => "#FFD700",
	"GoldenRod" => "#DAA520",
	"Gray" => "#808080",
	"Grey" => "#808080",
	"Green" => "#008000",
	"GreenYellow" => "#ADFF2F",
	"HoneyDew" => "#F0FFF0",
	"HotPink" => "#FF69B4",
	"IndianRed " => "#CD5C5C",
	"Indigo " => "#4B0082",
	"Ivory" => "#FFFFF0",
	"Khaki" => "#F0E68C",
	"Lavender" => "#E6E6FA",
	"LavenderBlush" => "#FFF0F5",
	"LawnGreen" => "#7CFC00",
	"LemonChiffon" => "#FFFACD",
	"LightBlue" => "#ADD8E6",
	"LightCoral" => "#F08080",
	"LightCyan" => "#E0FFFF",
	"LightGoldenRodYellow" => "#FAFAD2",
	"LightGray" => "#D3D3D3",
	"LightGrey" => "#D3D3D3",
	"LightGreen" => "#90EE90",
	"LightPink" => "#FFB6C1",
	"LightSalmon" => "#FFA07A",
	"LightSeaGreen" => "#20B2AA",
	"LightSkyBlue" => "#87CEFA",
	"LightSlateGray" => "#778899",
	"LightSlateGrey" => "#778899",
	"LightSteelBlue" => "#B0C4DE",
	"LightYellow" => "#FFFFE0",
	"Lime" => "#00FF00",
	"LimeGreen" => "#32CD32",
	"Linen" => "#FAF0E6",
	"Magenta" => "#FF00FF",
	"Maroon" => "#800000",
	"MediumAquaMarine" => "#66CDAA",
	"MediumBlue" => "#0000CD",
	"MediumOrchid" => "#BA55D3",
	"MediumPurple" => "#9370D8",
	"MediumSeaGreen" => "#3CB371",
	"MediumSlateBlue" => "#7B68EE",
	"MediumSpringGreen" => "#00FA9A",
	"MediumTurquoise" => "#48D1CC",
	"MediumVioletRed" => "#C71585",
	"MidnightBlue" => "#191970",
	"MintCream" => "#F5FFFA",
	"MistyRose" => "#FFE4E1",
	"Moccasin" => "#FFE4B5",
	"NavajoWhite" => "#FFDEAD",
	"Navy" => "#000080",
	"OldLace" => "#FDF5E6",
	"Olive" => "#808000",
	"OliveDrab" => "#6B8E23",
	"Orange" => "#FFA500",
	"OrangeRed" => "#FF4500",
	"Orchid" => "#DA70D6",
	"PaleGoldenRod" => "#EEE8AA",
	"PaleGreen" => "#98FB98",
	"PaleTurquoise" => "#AFEEEE",
	"PaleVioletRed" => "#D87093",
	"PapayaWhip" => "#FFEFD5",
	"PeachPuff" => "#FFDAB9",
	"Peru" => "#CD853F",
	"Pink" => "#FFC0CB",
	"Plum" => "#DDA0DD",
	"PowderBlue" => "#B0E0E6",
	"Purple" => "#800080",
	"Red" => "#FF0000",
	"RosyBrown" => "#BC8F8F",
	"RoyalBlue" => "#4169E1",
	"SaddleBrown" => "#8B4513",
	"Salmon" => "#FA8072",
	"SandyBrown" => "#F4A460",
	"SeaGreen" => "#2E8B57",
	"SeaShell" => "#FFF5EE",
	"Sienna" => "#A0522D",
	"Silver" => "#C0C0C0",
	"SkyBlue" => "#87CEEB",
	"SlateBlue" => "#6A5ACD",
	"SlateGray" => "#708090",
	"SlateGrey" => "#708090",
	"Snow" => "#FFFAFA",
	"SpringGreen" => "#00FF7F",
	"SteelBlue" => "#4682B4",
	"Tan" => "#D2B48C",
	"Teal" => "#008080",
	"Thistle" => "#D8BFD8",
	"Tomato" => "#FF6347",
	"Turquoise" => "#40E0D0",
	"Violet" => "#EE82EE",
	"Wheat" => "#F5DEB3",
	"White" => "#FFFFFF",
	"WhiteSmoke" => "#F5F5F5",
	"Yellow" => "#FFFF00",
	"YellowGreen" => "#9ACD32"
	);

	$i = 0;
	foreach ( $color_variables as $val ) {
		// Convert color names to hexagonal color values.
		$valsetting = $_REQUEST[$val["color_name"]];

		if (substr($valsetting,0,1) == "#") {
			$color_variables[$i]["color_value"] = strtoupper($valsetting);
			$check_images[$val["color_name"]] = strtoupper($valsetting);
		} else {
			$color_variables[$i]["color_value"] = $posscolornames[$valsetting];
			$check_images[$val["color_name"]] = $posscolornames[$valsetting];
		}
		$i = $i + 1;
	}
	if (!isset($schemename)) $schemename = $_REQUEST["schemename"];
	$schemename = str_replace(" ","_",$schemename);
	
	$PHORUM["mod_easy_colorscheme"]["schemes"][$schemename] = $color_variables;
	
	//Generate background images if needed
	$use_default = 0;
	if (isset($check_images["top_image_upper_color"]) 
		&& isset($check_images["top_image_lower_color"])) {
		
		$upper_color = trim($check_images["top_image_upper_color"],"#");
		$lower_color = trim($check_images["top_image_lower_color"],"#");

		if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["img_width"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["img_width"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["img_width"];
		if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["img_height"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["img_height"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["img_height"];

		if ($upper_color != "8BBB8B" || $lower_color != "619961") {
			$tpl_link = "mods/easy_colorscheme/tmp_images/".$schemename."_top_image.png";
			$img_file = "./".$tpl_link;
			$PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["use_images"] = 1;
			$PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["file"] = $tpl_link;
			if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["var"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["var"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["top_image"]["var"];
			if(!isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["var"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["var"] = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["var"];
			if(isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["img_width"])) {
				$img_width = $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["img_width"];
			} else {
				$img_width = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_width"];
			}
			if(isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["img_height"])) {
				$img_height = $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["top_image"]["img_height"];
			} else {
				$img_height = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["top_image"]["img_height"];
			}			
			$color_variables = array("upper_color"=>array("r_upper","g_upper","b_upper"), "lower_color"=>array("r_lower","g_lower","b_lower"));
	
			foreach ($color_variables as $variable => $subvariables) {
				foreach ($subvariables as $index => $subvariable) {
					$rgb = substr($$variable,$index * 2, 2);
					$$subvariable = hexdec($rgb);
					if ($$subvariable < 0 || $$subvariable > 255) {
						$use_default = 1;
					}
				}
			}

			if ($use_default != 1) {
				$im = imagecreate($img_width, $img_height);

				for ($i=0; $i < $img_height; $i++) {
					if ($r_lower - $r_upper != 0) {
						$r = $r_upper + ($r_lower - $r_upper) * ($i / $img_height);
					} else {
						$r = $r_upper;
					}
			
					if ($g_lower - $g_upper != 0) {
						$g = $g_upper + ($g_lower - $g_upper) * ($i / $img_height);
					} else {
						$g = $g_upper;
					}
	
					if ($b_lower - $b_upper != 0) {
						$b = $b_upper + ($b_lower - $b_upper) * ($i / $img_height);
					} else {
						$b = $b_upper;
					}
	
					$color = imagecolorallocate($im, $r, $g, $b);
					imageline($im, 0, $i, $img_width - 1, $i, $color);
				}
				
				imagepng($im, $img_file);
				imagedestroy($im);
			}
		}
	}
	$use_default = 0;
	if (isset($check_images["header_image_upper_color"]) 
		&& isset($check_images["header_image_lower_color"])) {
		
		$upper_color = trim($check_images["header_image_upper_color"],"#");
		$lower_color = trim($check_images["header_image_lower_color"],"#");
		if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["img_width"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["img_width"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["img_width"];
		if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["img_height"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["img_height"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["img_height"];
		
		if ($upper_color != "8BBB8B" || $lower_color != "4D894D") {
			$tpl_link = "mods/easy_colorscheme/tmp_images/".$schemename."_header_image.png";
			$img_file = "./".$tpl_link;
			$PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["use_images"] = 1;
			$PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["file"] = $tpl_link;
			if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["var"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["var"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["header_image"]["var"];
			if(!isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["var"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["var"] = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["var"];
			if(isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["img_width"])) {
				$img_width = $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["img_width"];
			} else {
				$img_width = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["img_width"];
			}
			if(isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["img_height"])) {
				$img_height = $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["header_image"]["img_height"];
			} else {
				$img_height = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["header_image"]["img_height"];
			}
			
			$color_variables = array("upper_color"=>array("r_upper","g_upper","b_upper"), "lower_color"=>array("r_lower","g_lower","b_lower"));

			foreach ($color_variables as $variable => $subvariables) {
				foreach ($subvariables as $index => $subvariable) {
					$rgb = substr($$variable,$index * 2, 2);
					$$subvariable = hexdec($rgb);
					if ($$subvariable < 0 || $$subvariable > 255) {
						$use_default = 1;
					}
				}
			}

			if ($use_default != 1) {
				$im = imagecreate($img_width, $img_height);

				for ($i=0; $i < $img_height; $i++) {
					if ($r_lower - $r_upper != 0) {
						$r = $r_upper + ($r_lower - $r_upper) * ($i / $img_height);
					} else {
						$r = $r_upper;
					}
			
					if ($g_lower - $g_upper != 0) {
						$g = $g_upper + ($g_lower - $g_upper) * ($i / $img_height);
					} else {
						$g = $g_upper;
					}
	
					if ($b_lower - $b_upper != 0) {
						$b = $b_upper + ($b_lower - $b_upper) * ($i / $img_height);
					} else {
						$b = $b_upper;
					}
	
					$color = imagecolorallocate($im, $r, $g, $b);
					imageline($im, 0, $i, $img_width - 1, $i, $color);
				}
				
				imagepng($im, $img_file);
				imagedestroy($im);
			}
		}
	}
	$use_default = 0;
	if (isset($check_images["message_image_upper_color"]) 
		&& isset($check_images["message_image_lower_color"])) {
		
		$upper_color = trim($check_images["message_image_upper_color"],"#");
		$lower_color = trim($check_images["message_image_lower_color"],"#");
		if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["img_width"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["img_width"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["img_width"];
		if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["img_height"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["img_height"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["img_height"];
		
		if ($upper_color != "EDF2ED" || $lower_color != "FFFFFF") {
			$tpl_link = "mods/easy_colorscheme/tmp_images/".$schemename."_message_image.png";
			$img_file = "./".$tpl_link;
			$PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["use_images"] = 1;
			$PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["file"] = $tpl_link;
			if(isset($PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["var"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["var"] = $PHORUM["mod_easy_colorscheme"]["temp_settings"]["image_info"]["message_image"]["var"];
			if(!isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["var"])) $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["var"] = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["var"];
			if(isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["img_width"])) {
				$img_width = $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["img_width"];
			} else {
				$img_width = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["img_width"];
			}
			if(isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["img_height"])) {
				$img_height = $PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename]["image_info"]["message_image"]["img_height"];
			} else {
				$img_height = $PHORUM["mod_easy_colorscheme"]["schemes_images"]["default_image_info"]["message_image"]["img_height"];
			}
			
			$color_variables = array("upper_color"=>array("r_upper","g_upper","b_upper"), "lower_color"=>array("r_lower","g_lower","b_lower"));
	
			foreach ($color_variables as $variable => $subvariables) {
				foreach ($subvariables as $index => $subvariable) {
					$rgb = substr($$variable,$index * 2, 2);
					$$subvariable = hexdec($rgb);
					if ($$subvariable < 0 || $$subvariable > 255) {
						$use_default = 1;
					}
				}
			}
			
			if ($use_default != 1) {
			
				$im = imagecreate($img_width, $img_height);
		
				for ($i=0; $i < $img_height; $i++) {
					if ($r_lower - $r_upper != 0) {
						$r = $r_upper + ($r_lower - $r_upper) * ($i / $img_height);
					} else {
						$r = $r_upper;
					}
			
					if ($g_lower - $g_upper != 0) {
						$g = $g_upper + ($g_lower - $g_upper) * ($i / $img_height);
					} else {
						$g = $g_upper;
					}
	
					if ($b_lower - $b_upper != 0) {
						$b = $b_upper + ($b_lower - $b_upper) * ($i / $img_height);
					} else {
						$b = $b_upper;
					}
	
					$color = imagecolorallocate($im, $r, $g, $b);
					imageline($im, 0, $i, $img_width - 1, $i, $color);
				}
				
				imagepng($im, $img_file);
				imagedestroy($im);
			}
		}
	}		

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	$PHORUM["mod_easy_colorscheme"]["settings_log"] = "<b style='color:#009900'>$settings_log_title to the scheme \"$schemename\" successful.</b>";
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
	
  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
  
	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme&viewscheme=".$schemename.$admin_token );
}	

// Delete a color scheme
// Called by ECS_MODE = Delete

function run_delete_page() {

	$PHORUM = $GLOBALS["PHORUM"];

	$schemename = $_REQUEST["deletescheme"];
	
	unset($PHORUM["mod_easy_colorscheme"]["schemes"][$schemename]);
	if (isset($PHORUM["mod_easy_colorscheme"]["schemes_images"][$schemename])) {
		$image_dir = opendir("./mods/easy_colorscheme/tmp_images");
			while ($image_file = readdir($image_dir)) {
		        if (strstr($image_file,".png")) {
		        	$test = strstr($image_file,$schemename);
		        	if ($test === false) {
		        	} else {
			        	unlink("./mods/easy_colorscheme/tmp_images/".$image_file);
			        }
		        }
		    }
		closedir($image_dir);
	}


	if ($PHORUM["mod_easy_colorscheme"]["usescheme"] == $schemename) {
		unset($PHORUM["mod_easy_colorscheme"]["usescheme"]);
	}
	
	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	$PHORUM["mod_easy_colorscheme"]["settings_log"] = "<b style='color:#009900'>The scheme \"$schemename\" has been deleted.</b>";
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));

  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
  
	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token );

}

// Apply a color scheme to all forums
// Called by ECS_MODE = ApplyAll
function run_applyall_page() {
	
	$PHORUM = $GLOBALS["PHORUM"];
	
	$schemename = $_REQUEST["applyscheme"];
	$PHORUM["mod_easy_colorscheme"]["usescheme"] = $schemename;
	$PHORUM["mod_easy_colorscheme"]["useschemebyforum"] = false;

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));

  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
  
	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token);
}


// Toggle the use of specific color schemes per forum
// Called by ECS_MODE = ToggleSpecific
function run_togglespecific_page() {
	
	$PHORUM = $GLOBALS["PHORUM"];
		
	$PHORUM["mod_easy_colorscheme"]["useschemesbyforum"] = $_REQUEST["applyspecifictoggle"];

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));

  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
  
	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token);
}

// Toggle the use of specific color schemes per template
// Called by ECS_MODE = ToggleSpecificTemplate
function run_togglespecifictemplate_page() {

	$PHORUM = $GLOBALS["PHORUM"];

	$PHORUM["mod_easy_colorscheme"]["useschemesbytemplate"] = $_REQUEST["applyspecifictemplatetoggle"];

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));

  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
  
	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token);
}

// Apply a color scheme to a specific forum
// Called by ECS_MODE = ApplySpecific
function run_applyspecific_page() {
	
	global $PHORUM;
	
	$schemename = $_REQUEST["applyscheme"];
	$specificforum = $_REQUEST["applytoforum"];
	
	$PHORUM["mod_easy_colorscheme"]["forumschemes"][$specificforum] = $schemename;

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));

  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
  
	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token);
}

// Apply a color scheme to a specific template
// Called by ECS_MODE = ApplySpecificTemplate
function run_applyspecifictemplate_page() {
	
	global $PHORUM;
	
	$schemename = $_REQUEST["applyscheme"];
	$specifictemplate = $_REQUEST["applytotemplate"];

	$PHORUM["mod_easy_colorscheme"]["templateschemes"][$specifictemplate] = $schemename;

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));

  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];
  
	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token);
}

// Toggle debugging of the settings page
// Called by ECS_MODE = ToggleDebugging
function run_toggledebugging_page() {

	$PHORUM = $GLOBALS["PHORUM"];

	$PHORUM["mod_easy_colorscheme"]["debugtoggle"] = $_REQUEST["debugtoggle"];

	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));

  $admin_token = empty($PHORUM['admin_token']) ? "" : "&phorum_admin_token=".$PHORUM['admin_token'];

	header( "Location:./admin.php?module=modsettings&mod=easy_colorscheme".$admin_token);
}

// Run appropriate functions as determined by ECS_MODE
try {
if (isset($_REQUEST["ECS_MODE"])) {
	if ($_REQUEST["ECS_MODE"] == "Save") {
		$GLOBALS["ecs_debug"][] = "starting save page";
		run_save_page();
	} else if ($_REQUEST["ECS_MODE"] == "ApplyAll") {
		$GLOBALS["ecs_debug"][] = "starting applyall page";
		run_applyall_page();
	} else if ($_REQUEST["ECS_MODE"] == "ApplySpecific") {
		$GLOBALS["ecs_debug"][] = "starting apply specific page";
		run_applyspecific_page();
	} else if ($_REQUEST["ECS_MODE"] == "ApplySpecificTemplate") {
		$GLOBALS["ecs_debug"][] = "starting apply specific template page";
		run_applyspecifictemplate_page();
	} else if ($_REQUEST["ECS_MODE"] == "Delete") {
		$GLOBALS["ecs_debug"][] = "starting delete page";
		run_delete_page();
	} else if ($_REQUEST["ECS_MODE"] == "ImportFile") {
		$GLOBALS["ecs_debug"][] = "starting save page to import file";
		run_save_page();
	} else if ($_REQUEST["ECS_MODE"] == "ToggleSpecific") {
		$GLOBALS["ecs_debug"][] = "starting toggle specific page";
		run_togglespecific_page();
	} else if ($_REQUEST["ECS_MODE"] == "ToggleSpecificTemplate") {
		$GLOBALS["ecs_debug"][] = "starting toggle specific template page";
		run_togglespecifictemplate_page();
	} else if ($_REQUEST["ECS_MODE"] == "ToggleDebugging") {
		$GLOBALS["ecs_debug"][] = "starting toggle debugging page";
		run_toggledebugging_page();
	} else if ($_REQUEST["ECS_MODE"] == "ExportList") {
		$GLOBALS["ecs_debug"][] = "starting normal page for export list";
		run_normal_page();
	}
} else {
	$GLOBALS["ecs_debug"][] = "starting normal page";
	run_normal_page();
}
} catch (Exception $err) {
	$GLOBALS["ecs_debug"][] = $err;
}
if (!empty($PHORUM["mod_easy_colorscheme"]["debugtoggle"]) || isset($_REQUEST["do_debugging"])) {
	print "<p><b>Completed steps:</b><br />";
	foreach($GLOBALS["ecs_debug"] as $debug_info) {
		print " - $debug_info<br />";
	}
	print "</p>";
}
//*/
?>
