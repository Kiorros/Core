<?php

if(!defined("PHORUM")) return;

function phorum_mod_easy_colorscheme_common () {

$PHORUM = $GLOBALS["PHORUM"];

// Generate preview with appropriate colors
if (isset($PHORUM["mod_easy_colorscheme"]["previewscheme"])) {
	$viewscheme=$PHORUM["mod_easy_colorscheme"]["previewscheme"];
	$PHORUM["mod_easy_colorscheme"]["previewscheme"] = $viewscheme;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
	foreach ($PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme] as $key => $val) {
		$color_name = $val["color_name"];
		$GLOBALS["PHORUM"]["DATA"][$color_name] = $val["color_value"];
	}
	if (!empty($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["use_images"])) {
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["image_info"] as $key => $val) {
			if (!empty($val["var"])) {
				$var_name = $val["var"];
				$GLOBALS["PHORUM"]["DATA"][$var_name] = $val["file"];
			}
		}
	}
	
	unset($PHORUM["mod_easy_colorscheme"]["previewscheme"]);
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
} elseif (isset($_REQUEST["easycolorschemepreview"])) {
	$viewscheme=$_REQUEST["easycolorschemepreview"];
	$PHORUM["mod_easy_colorscheme"]["previewscheme"] = $viewscheme;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
	foreach ($PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme] as $key => $val) {
		$color_name = $val["color_name"];
		$GLOBALS["PHORUM"]["DATA"][$color_name] = $val["color_value"];
	}
	if (!empty($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["use_images"])) {
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["image_info"] as $key => $val) {
			if (!empty($val["var"])) {
				$var_name = $val["var"];
				$GLOBALS["PHORUM"]["DATA"][$var_name] = $val["file"];
			}
		}
	}
	
	$GLOBALS["PHORUM"]["DATA"]["HTML_TITLE"] = "Previewing the \"".$viewscheme."\" color scheme.";
	return;
	
// Generate pages with appropriate color scheme per forum
} elseif (isset($PHORUM["mod_easy_colorscheme"]["useschemesbyforum"]) && $PHORUM["mod_easy_colorscheme"]["useschemesbyforum"]) {
	if (isset($PHORUM["mod_easy_colorscheme"]["forumschemes"][$PHORUM["forum_id"]]) && $PHORUM["mod_easy_colorscheme"]["forumschemes"][$PHORUM["forum_id"]]) {
		$viewscheme=$PHORUM["mod_easy_colorscheme"]["forumschemes"][$PHORUM["forum_id"]];
		if (!$PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme]) {
			if ($PHORUM["mod_easy_colorscheme"]["usescheme"]) {
				$viewscheme=$PHORUM["mod_easy_colorscheme"]["usescheme"];
			} else {
				return;
			}
		}
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme] as $key => $val) {
			$color_name = $val["color_name"];
			$GLOBALS["PHORUM"]["DATA"][$color_name] = $val["color_value"];
		}
		if (!empty($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["use_images"])) {
			foreach ($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["image_info"] as $key => $val) {
				if (!empty($val["var"])) {
					$var_name = $val["var"];
					$GLOBALS["PHORUM"]["DATA"][$var_name] = $val["file"];
				}
			}
		}

// If a forum has no specific scheme, use the general scheme (if applied)
	} elseif (isset($PHORUM["mod_easy_colorscheme"]["usescheme"]) && $PHORUM["mod_easy_colorscheme"]["usescheme"]) {
		$viewscheme=$PHORUM["mod_easy_colorscheme"]["usescheme"];
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme] as $key => $val) {
			$color_name = $val["color_name"];
			$GLOBALS["PHORUM"]["DATA"][$color_name] = $val["color_value"];
		}
		if (!empty($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["use_images"])) {
			foreach ($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["image_info"] as $key => $val) {
				if (!empty($val["var"])) {
					$var_name = $val["var"];
					$GLOBALS["PHORUM"]["DATA"][$var_name] = $val["file"];
				}
			}
		}
	}

// Generate pages with appropriate color scheme per template
} elseif (isset($PHORUM["mod_easy_colorscheme"]["useschemesbytemplate"]) && $PHORUM["mod_easy_colorscheme"]["useschemesbytemplate"]) {

    if ( ( !isset( $PHORUM['display_fixed'] ) || !$PHORUM['display_fixed'] ) && isset( $PHORUM['user']['user_template'] ) && !empty($PHORUM['user']['user_template'])) {
        $PHORUM["DATA"]['TEMPLATE'] = $PHORUM['user']['user_template'];
    }
    if (empty($PHORUM["DATA"]["TEMPLATE"])) {
        $PHORUM["DATA"]["TEMPLATE"] = $PHORUM["default_forum_options"]["template"];
    }
	if ($PHORUM["mod_easy_colorscheme"]["templateschemes"][$PHORUM["DATA"]["TEMPLATE"]]) {
		$viewscheme=$PHORUM["mod_easy_colorscheme"]["templateschemes"][$PHORUM["DATA"]["TEMPLATE"]];
		if (!$PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme]) {
			if ($PHORUM["mod_easy_colorscheme"]["usescheme"]) {
				$viewscheme=$PHORUM["mod_easy_colorscheme"]["usescheme"];
			} else {
				return;
			}
		}
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme] as $key => $val) {
			$color_name = $val["color_name"];
			$GLOBALS["PHORUM"]["DATA"][$color_name] = $val["color_value"];
		}
		if (!empty($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["use_images"])) {
			foreach ($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["image_info"] as $key => $val) {
				if (!empty($val["var"])) {
					$var_name = $val["var"];
					$GLOBALS["PHORUM"]["DATA"][$var_name] = $val["file"];
				}
			}
		}
// If a template has no specific scheme, use the general scheme (if applied)
	} elseif (isset($PHORUM["mod_easy_colorscheme"]["usescheme"]) && $PHORUM["mod_easy_colorscheme"]["usescheme"]) {
		$viewscheme=$PHORUM["mod_easy_colorscheme"]["usescheme"];
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme] as $key => $val) {
			$color_name = $val["color_name"];
			$GLOBALS["PHORUM"]["DATA"][$color_name] = $val["color_value"];
		}
		if (!empty($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["use_images"])) {
			foreach ($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["image_info"] as $key => $val) {
				if (!empty($val["var"])) {
					$var_name = $val["var"];
					$GLOBALS["PHORUM"]["DATA"][$var_name] = $val["file"];
				}
			}
		}
	}

// Generate pages with appropriate color scheme (no changes if no scheme selected)
} elseif (isset($PHORUM["mod_easy_colorscheme"]["usescheme"]) && $PHORUM["mod_easy_colorscheme"]["usescheme"]) {
	$viewscheme=$PHORUM["mod_easy_colorscheme"]["usescheme"];
	foreach ($PHORUM["mod_easy_colorscheme"]["schemes"][$viewscheme] as $key => $val) {
		$color_name = $val["color_name"];
		$GLOBALS["PHORUM"]["DATA"][$color_name] = $val["color_value"];
	}
	if (!empty($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["use_images"])) {
		foreach ($PHORUM["mod_easy_colorscheme"]["schemes_images"][$viewscheme]["image_info"] as $key => $val) {
			if (!empty($val["var"])) {
				$var_name = $val["var"];
				$GLOBALS["PHORUM"]["DATA"][$var_name] = $val["file"];
			}
		}
	}
}
if (isset($PHORUM["mod_easy_colorscheme"]["previewscheme"])) {
	unset($PHORUM["mod_easy_colorscheme"]["previewscheme"]);
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
}

return ;

}

function phorum_mod_easy_colorscheme_empty_css() { return ""; }

function phorum_mod_easy_colorscheme_css_register($data) {

global $PHORUM;

if (isset($PHORUM["mod_easy_colorscheme"]["previewscheme"])) {
	$PHORUM['mod_easy_colorscheme']['css_version'] =
		isset($PHORUM['mod_easy_colorscheme']['css_version'])
		? $PHORUM['mod_easy_colorscheme']['css_version'] + 1 : 1;
	phorum_db_update_settings(array("mod_easy_colorscheme" => $PHORUM["mod_easy_colorscheme"]));
}

$data['register'][] = array(
	"module"    => "easy_colorscheme",
	"where"     => "after",
	"source"    => "function(phorum_mod_easy_colorscheme_empty_css)",
	"cache_key" => $PHORUM['mod_easy_colorscheme']['css_version']
);

return $data;
    
}
?>
