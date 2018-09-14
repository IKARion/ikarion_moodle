<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme ildcustom lib.
 *
 * @package    theme_ildcustom
 * @copyright  2015 Fachhochschule L&uuml;beck, www.fh-luebeck.de
 * @autor	   Miriam Kunst
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function theme_ildcustom_process_css($css, $theme) {

    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');
	$css = theme_ildcustom_set_logo($css, $logo);
	
	// Set the background image for the logo.
    $bannerbgimage = $theme->setting_file_url('bannerbgimage', 'bannerbgimage');
	$css = theme_ildcustom_set_bannerbgimage($css, $bannerbgimage);
	
	// Set the background foreground layer image.
    $bannerlayerimage = $theme->setting_file_url('bannerlayerimage', 'bannerlayerimage');
	$css = theme_ildcustom_set_bannerlayerimage($css, $bannerlayerimage);
	
	// Set the banner content image for the logo.
    $bannercontentimage = $theme->setting_file_url('bannercontentimage', 'bannercontentimage');
	$css = theme_ildcustom_set_bannercontentimage($css, $bannercontentimage);
	
	// Set the background image for the loginsite + loginblock.
    $loginbgimage = $theme->setting_file_url('loginbgimage', 'loginbgimage');
	$css = theme_ildcustom_set_loginbgimage($css, $loginbgimage);
	
	// Set the ICO image for the favicon.
    $favicon = $theme->setting_file_url('favicon', 'favicon');
	$css = theme_ildcustom_set_faviconcss($css, $favicon);
	
	// Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_ildcustom_set_customcss($css, $customcss);

    return $css;
}


function theme_ildcustom_set_logo($css, $logo) {
    $logotag = '[[setting:logo]]';
    
	if (is_null($logo)) {
        $replacement = '';
    } else {
        $replacement = $logo;
    }
    $css = str_replace($logotag, $replacement, $css);

    return $css;
}

function theme_ildcustom_set_bannerbgimage($css, $bannerbgimage) {
    $bannerbgimagetag = '[[setting:bannerbgimage]]';
    if (is_null($bannerbgimage)) {
        $replacement = '';
    } else {
        $replacement = $bannerbgimage;
    }
    $css = str_replace($bannerbgimagetag, $replacement, $css);
    return $css;
}

function theme_ildcustom_set_bannerlayerimage($css, $bannerlayerimage) {
    $bannerlayerimagetag = '[[setting:bannerlayerimage]]';
    if (is_null($bannerlayerimage)) {
        $replacement = '';
    } else {
        $replacement = $bannerlayerimage;
    }
    $css = str_replace($bannerlayerimagetag, $replacement, $css);
    return $css;
}
function theme_ildcustom_set_bannercontentimage($css, $bannercontentimage) {
    $bannercontentimagetag = '[[setting:bannercontentimage]]';
    if (is_null($bannercontentimage)) {
        $replacement = '';
    } else {
        $replacement = $bannercontentimage;
    }
    $css = str_replace($bannercontentimagetag, $replacement, $css);
    return $css;
}

function theme_ildcustom_set_loginbgimage($css, $loginbgimage) {
    $loginbgimagetag = '[[setting:loginbgimage]]';
    if (is_null($loginbgimage)) {
        $replacement = '';
    } else {
        $replacement = $loginbgimage;
    }
    $css = str_replace($loginbgimagetag, $replacement, $css);
    return $css;
}

function theme_ildcustom_set_faviconcss($css, $favicon) {
    $faviconimagetag = '[[setting:favicon]]';
    if (is_null($favicon)) {
        $replacement = '';
    } else {
        $replacement = $favicon;
    }
    $css = str_replace($faviconimagetag, $replacement, $css);
    return $css;
}


/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_ildcustom_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
   if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo'|| $filearea === 'bannerbgimage'|| $filearea === 'bannerlayerimage'|| $filearea === 'bannercontentimage'|| $filearea === 'loginbgimage' || $filearea === 'favicon')) {
        $theme = theme_config::load('ildcustom');
		// By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Extra LESS code to inject.
 *
 * This will generate some LESS code from the settings used by the user. We cannot use
 * the {@link theme_ildcustom_less_variables()} here because we need to create selectors or
 * alter existing ones.
 *
 * @param theme_config $theme The theme config object.
 * @return string Raw LESS code.
 */

function theme_ildcustom_extra_less($theme) {
    $content = '';
    $imageurl = $theme->setting_file_url('bannerbgimage', 'bannerbgimage');
    // Sets the background image, and its settings.
    
	if (!empty($imageurl)) {
        $content .= '.header-banner { ';
        // $content .= "background-image: url('$imageurl');";

        if (!empty($theme->settings->bannerbgposition)) {
            $content .= 'background-position: ' . str_replace('_', ' ', $theme->settings->bannerbgposition) . ';';
        }
        if (!empty($theme->settings->bannerbgrepeat)) {
            $content .= 'background-repeat: ' . $theme->settings->bannerbgrepeat . ';';
        } 
		if (!empty($theme->settings->bannerbgsize)) {
            $content .= 'background-size: ' . $theme->settings->bannerbgsize . ';';
        } 
        $content .= ' }';
    }
	
	$nextimageurl = $theme->setting_file_url('bannerlayerimage', 'bannerlayerimage');
    // Sets the background image, and its settings.
    
	if (!empty($nextimageurl)) {
        $content .= '.header-banner-inside { ';
        // $content .= "background-image: url('$imageurl');";

        if (!empty($theme->settings->bannerlayerposition)) {
            $content .= 'background-position: ' . str_replace('_', ' ', $theme->settings->bannerlayerposition) . ';';
        }
        if (!empty($theme->settings->bannerlayerrepeat)) {
            $content .= 'background-repeat: ' . $theme->settings->bannerlayerrepeat . ';';
        } 
		if (!empty($theme->settings->bannerlayersize)) {
            $content .= 'background-size: ' . $theme->settings->bannerlayersize . ';';
        } 
        $content .= ' }';
    }
	
	$insideimageurl = $theme->setting_file_url('bannercontentimage', 'bannercontentimage');
    // Sets the background image, and its settings.
	
	if (!empty($insideimageurl)) {
        $content .= '.header-banner-main { ';
        // $content .= "background-image: url('$insideimageurl');";

        if (!empty($theme->settings->bannercontentposition)) {
            $content .= 'background-position: ' . str_replace('_', ' ', $theme->settings->bannercontentposition) . ';';
        }
        if (!empty($theme->settings->bannercontentrepeat)) {
            $content .= 'background-repeat: ' . $theme->settings->bannercontentrepeat . ';';
        } 
		/*
		if (!empty($theme->settings->bannercontentsize)) {
            $content .= 'background-size: ' . $theme->settings->bannercontentsize . ';';
        } */
		if (!empty($theme->settings->bannercontentfix)) {
            $content .= 'background-size: ' . $theme->settings->bannercontentfix . ';';
        }
        $content .= ' }';
    }
	
	$loginimageurl = $theme->setting_file_url('loginbgimage', 'loginbgimage');
    // Sets the background image, and its settings.
    
	if (!empty($loginimageurl)) {
        $content .= '#page-login-index #page-wrapper { ';
        $content .= 'background-image: url(' . $loginimageurl . ');';

        if (!empty($theme->settings->loginbgposition)) {
            $content .= 'background-position: ' . str_replace('_', ' ', $theme->settings->loginbgposition) . ';';
        }
        if (!empty($theme->settings->loginbgrepeat)) {
            $content .= 'background-repeat: ' . $theme->settings->loginbgrepeat . ';';
        } 
		if (!empty($theme->settings->loginbgsize)) {
            $content .= 'background-size: ' . $theme->settings->loginbgsize . ';';
        } 
        $content .= ' }';
    }
	// Ein- und Ausblenden der Headerbanners auf der Websitestartseite (bei Loginblock Fullsizeoben)
	if (!empty($theme->settings->loginblockheader)) {
			$content .= '#page-site-index.notloggedin .header-banner{display:none; visibility:hidden;}';
	} else { $content .= '#page-site-index.notloggedin .header-banner{display:block; visibility:visible;}'; }
	
	
	if (!empty($theme->settings->customless)) {
		$extraless = $theme->settings->customless;
		if (!empty($extraless)) {
			$content .= $extraless;
		}
	}
		
    return $content;
}


/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_ildcustom_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    if (is_null($customcss)) {
        $replacement = '';
    } else {
        $replacement = $customcss;
    }
    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/** ------------------------------------------------------------------------------
*  ----------------------------------------------------------------------------- */

function theme_ildcustom_less_variables($theme) {
    if (empty($theme)) {  // Child theme needs to supply 'null' so that we use our 'theme_config' object instead.
        $theme = \theme_config::load('ildcustom');
    }
    $variables = array();

    if (!empty($theme->settings->themecolor)) {
        $variables['themecolor'] = $theme->settings->themecolor;
    }
	else { $variables['themecolor'] = '#d32121';
	}
	if (!empty($theme->settings->navbarcolor)) {
        $variables['navbarcolor'] = $theme->settings->navbarcolor;
    }
	else { $variables['navbarcolor'] = '#ffffff';
	}
	
	if (!empty($theme->settings->blockcolor)) {
        $variables['blockcolor'] = $theme->settings->blockcolor;
    }
	else { $variables['blockcolor'] = '#f0f1f3';
	}
	
	if (!empty($theme->settings->linkcolor)) {
        $variables['linkcolor'] = $theme->settings->linkcolor;
    }
	else { $variables['linkcolor'] = '#275e8f';
	}
	
	if (!empty($theme->settings->textcolor)) {
        $variables['textcolor'] = $theme->settings->textcolor;
    }
	else { $variables['textcolor'] = '#333333';
	}
	
	if (!empty($theme->settings->navtextcolor)) {
        $variables['navtextcolor'] = $theme->settings->navtextcolor;
    }
	else { 
		if (!empty($theme->settings->textcolor)) {
			$variables['navtextcolor'] = $theme->settings->textcolor;
		}
		else { $variables['navtextcolor'] = '#333333';
		}
	}
	
    if (!empty($theme->settings->footercolor)) {
        $variables['footercolor'] = $theme->settings->footercolor;
    }
	else { $variables['footercolor'] = '#eeeeee';
	}
	
    // Werte an LESS
	if (!empty($theme->settings->brandwidth)) {
        $variables['brandwidth'] = $theme->settings->brandwidth . 'px';
    }
	else { $variables['brandwidth'] = '138px';
	}

	if (!empty($theme->settings->navheight)) {
        $variables['navheight'] = $theme->settings->navheight . 'px';
    }
	else { $variables['navheight'] = '52px';
	}
	if (!empty($theme->settings->navtopborder)) {
        $variables['navtopborder'] = $theme->settings->navtopborder . 'px';
    }
	else { $variables['navtopborder'] = '0px';
	}
	if (!empty($theme->settings->navbottomborder)) {
        $variables['navbottomborder'] = $theme->settings->navbottomborder . 'px';
    }
	else { $variables['navbottomborder'] = '0px';
	}
	
	if (!empty($theme->settings->navbordercolor)) {
        $variables['navbordercolor'] = $theme->settings->navbordercolor;
    }
	else { 
		if (!empty($theme->settings->navbarcolor)) {
			$variables['navbordercolor'] = $theme->settings->navbarcolor;
		}
		else { $variables['navbordercolor'] = '#fff';
		}
	}
	
	if (!empty($theme->settings->bannerheight)) {
        $variables['bannerheight'] = $theme->settings->bannerheight . 'px';
    }
	else { $variables['bannerheight'] = '170px';
	}
	
	if (!empty($theme->settings->bannerbgcolor)) {
        $variables['bannerbgcolor'] = $theme->settings->bannerbgcolor;
    }
	else { $variables['bannerbgcolor'] = '#dedede';
	}
	
	
	if (!empty($theme->settings->loginbgcolor)) {
        $variables['loginbgcolor'] = $theme->settings->loginbgcolor;
    }
	else { $variables['loginbgcolor'] = '#dedede';
	}

	if (!empty($theme->settings->loginbgposition)) {
		 $variables['loginbgposition'] = $theme->settings->loginbgcolor;
    }
	else { $variables['loginbgposition'] = 'center center';
	}
	
	if (!empty($theme->settings->loginbgrepeat)) {
		 $variables['loginbgrepeat'] = $theme->settings->loginbgrepeat;
    }
	else { $variables['loginbgrepeat'] = 'no-repeat';
	}
	
	if (!empty($theme->settings->loginbgsize)) {
		 $variables['loginbgsize'] = $theme->settings->loginbgsize;
    }
	else { $variables['loginbgsize'] = 'cover';
	}
	
    return $variables;
}

/* ------------------------------------------------------------------- */
/**
 * Output favicon URL
 *
 */

function ildcustom_get_favicon_url($theme, $output) {
    $faviconimage = $theme->setting_file_url('favicon', 'favicon');
    if (!empty($faviconimage)) {
        $faviconurl = $faviconimage;
    } else {
        $faviconurl = $output->favicon();
    }
    return $faviconurl;
}


/* ------------------------------------------------------------------- */

/**
 * Output of the regions
 *
 */
function ildcustom_grid($hassidepre, $hassidepost, $hassidefullsizetop, $hassidetop, $hascontenttop, $hascontentbottom, $hassidebottom , $hassidefooter) {

    if ($hassidepre && $hassidepost) {
        $regions = array('content' => 'col-sm-6 col-sm-push-3');
        $regions['pre'] = 'col-sm-3 col-sm-pull-6';
        $regions['post'] = 'col-sm-3';
    } else if ($hassidepre && !$hassidepost) {
        $regions = array('content' => 'col-md-9 col-sm-12 col-xs-12'); /* col-sm-9 */ 
        $regions['pre'] = 'col-md-3 col-sm-12 col-xs-12'; /* col-sm-3 */ 
        $regions['post'] = 'empty';
    } else if (!$hassidepre && $hassidepost) {
        $regions = array('content' => 'col-md-9 col-sm-12 col-xs-12');
        $regions['pre'] = 'empty';
        $regions['post'] = 'col-md-3 col-sm-12 col-xs-12';
    } else if (!$hassidepre && !$hassidepost) {
        $regions = array('content' => 'col-md-12');
        $regions['pre'] = 'empty';
        $regions['post'] = 'empty';
    }
	
	
    if ('rtl' === get_string('thisdirection', 'langconfig')) {
        if ($hassidepre && $hassidepost) {
            $regions['pre'] = 'col-sm-3  col-sm-push-3';
            $regions['post'] = 'col-sm-3 col-sm-pull-9';
        } else if ($hassidepre && !$hassidepost) {
            $regions = array('content' => 'col-md-9 col-sm-12 col-xs-12');
            $regions['pre'] = 'col-md-3 col-sm-12 col-xs-12';
            $regions['post'] = 'empty';
        } else if (!$hassidepre && $hassidepost) {
            $regions = array('content' => 'col-md-9 col-sm-12 col-xs-12');
            $regions['pre'] = 'empty';
            $regions['post'] = 'col-md-3 col-sm-12 col-xs-12';
        }
    }
	
	/* Neue Blockbereiche ganzseitig */
	if ($hassidefullsizetop) {
        $regions['fullsize-top'] = 'fullsize';
	} else {
		$regions['fullsize-top'] = 'empty';
	}
	if ($hassidetop) {
        $regions['side-top'] = 'col-md-12 col-sm-12 col-xs-12';
	} else {
		$regions['side-top'] = 'empty';
	}
	if ($hascontenttop) {
        $regions['content-top'] = 'col-md-12 col-sm-12 col-xs-12';
	} else {
		$regions['content-top'] = 'empty';
	}
	if ($hascontentbottom) {
        $regions['content-bottom'] = '';
	} else {
		$regions['content-bottom'] = 'empty';
	}
	if ($hassidefooter) {
        $regions['side-footer'] = 'col-md-12 col-sm-12 col-xs-12';
	} else {
		$regions['side-footer'] = 'empty';
	}	
	
    return $regions;
}


