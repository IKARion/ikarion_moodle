<?php
// This file is part of The ildcustom Moodle theme
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

 
// Variablen fuer die Ausgabe der Blockbereiche
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$hassidefullsizetop = $PAGE->blocks->region_has_content('fullsize-top', $OUTPUT);
$hassidetop = $PAGE->blocks->region_has_content('side-top', $OUTPUT);
$hascontenttop = $PAGE->blocks->region_has_content('content-top', $OUTPUT);
$hascontentbottom = $PAGE->blocks->region_has_content('content-bottom', $OUTPUT);
$hassidebottom = $PAGE->blocks->region_has_content('side-bottom', $OUTPUT);
$hassidefooter = $PAGE->blocks->region_has_content('side-footer', $OUTPUT);

$knownregionpre = $PAGE->blocks->is_known_region('side-pre');
$knownregionpost = $PAGE->blocks->is_known_region('side-post');

$knownregionfullsizetop = $PAGE->blocks->is_known_region('fullsize-top');
$knownregiontop = $PAGE->blocks->is_known_region('side-top');
$knownregioncontenttop = $PAGE->blocks->is_known_region('content-top');
$knownregioncontentbottom = $PAGE->blocks->is_known_region('content-bottom');
$knownregionbottom = $PAGE->blocks->is_known_region('side-bottom');
$knownregionfooter = $PAGE->blocks->is_known_region('side-footer');

$regions = ildcustom_grid($hassidepre, $hassidepost, $hassidefullsizetop, $hassidetop, $hascontenttop, $hascontentbottom, $hassidebottom , $hassidefooter);
$PAGE->set_popup_notification_allowed(false);
$faviconoutput=$PAGE->theme->setting_file_url('favicon','favicon');
$setfavicon= ildcustom_get_favicon_url($PAGE->theme, $OUTPUT);


echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $setfavicon; ?>" /> <?php /* echo $OUTPUT->favicon(); */?>
    <?php echo $OUTPUT->standard_head_html(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php 
	echo $OUTPUT->standard_top_of_body_html(); 
?>

<div id="side-container">
<nav role="navigation" class="navbar navbar-default">
    <div class="container">
   <?php /* brand */ ?>
	
    <div class="navbar-header pull-right">
		<?php 
			echo $OUTPUT->user_menu(); 
		?>
        <?php 
			// echo $OUTPUT->navbar_button(); 
		?>
    </div>
	

    <div id="moodle-navbar" class="nav navbar-right"> <?php // navbar-collapse collapse ?>
        <ul class="nav navbar-nav pull-right">
            <li>
		<?php 
			if (isloggedin()) {
				global $DB;
				$unread_messages = $DB->count_records_select('message', "useridto = ?", array($USER->id), "COUNT('id')");
				$style = 'pull-right ild-read-messages';
				if ($unread_messages > 0) {
					$style = 'ild-unread-messages';
				}
				echo html_writer::link(
						new moodle_url('/message/index.php'), 
						'<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span><span class="nav-message-counter"> <sup>'.$unread_messages.'</sup></span>',
						array('class' => $style)
					);
			}
		?>
		</li>
        </ul> 
		
		<?php echo $OUTPUT->custom_menu(); ?>
				
        <ul class="nav pull-right">
            <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
        </ul> 
    </div>
    
	<div class="navbar-header pull-left">
		<a class="navbar-brand" href="<?php echo $CFG->wwwroot;?>"><?php 
			$brandnote = 'theme_'.$PAGE->theme->name.'_brand_html';
			if ((isset($CFG->$brandnote)) && ($CFG->$brandnote != '')) {
				echo $CFG->$brandnote;
			} ?></a>
    </div>
	
    </div>
</nav>
<div class="header-banner">
  <div class="header-banner-inside">
	<div class="header-banner-main container">
		<?php 
			$themegrafikbanner = 'theme_'.$PAGE->theme->name.'_graphicbanner_text';
			if ((isset($CFG->$themegrafikbanner)) && ($CFG->$themegrafikbanner != '')) {
				echo '<h1>'.$CFG->$themegrafikbanner.'</h1>';
			}
		?>
	</div>
  </div>	
</div>
<?php 
    if ($knownregionfullsizetop) {
        echo $OUTPUT->blocks('fullsize-top', $regions['fullsize-top']);
    } ?> 
<div id="page-wrapper">	
<div id="page" class="container">
	<?php 
    if ($knownregiontop) {
        echo $OUTPUT->blocks('side-top', 'col-md-12 col-sm-12 col-xs-12');
    } ?> 
    <?php echo $OUTPUT->full_header(); ?>
<?php
if ($knownregionpost) {
	echo "<a id='post-block-toggle' data-toggleflag='1' data-vp='n' data-originaltop='0' class='block-toggle pull-right'></a>";
} ?>	

	
  
  
    <div id="page-content" class="row">
		<?php 
		if ($knownregioncontenttop) {
			echo $OUTPUT->blocks('content-top', 'col-md-12 col-sm-12 col-xs-12');
		} ?> 
		
        <section id="region-main" class="<?php echo $regions['content']; ?>">
		  <div id="region-main-inner">
		    <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
			if ($knownregioncontentbottom) {
				echo $OUTPUT->blocks('content-bottom', $regions['content-bottom']);
			}
            ?>		
		  </div>
		</section>
        <?php
        if ($knownregionpre) {
            echo $OUTPUT->blocks('side-pre', $regions['pre']);
        }?>
        <?php
        if ($knownregionpost) {
            echo $OUTPUT->blocks('side-post', $regions['post']);
        }?>
    </div>
		<?php 
        if ($knownregionbottom) {
            echo $OUTPUT->blocks('side-bottom', 'col-md-12 col-sm-12 col-xs-12');
    } ?>
</div>
	<?php 
        if ($knownregionfooter) {
            echo $OUTPUT->blocks('side-footer', $regions['side-footer']);
    } ?>
</div>
<footer id="page-footer">
	<div id="page-footer-container" class="container">
	  <div id="page-footer-content" class="row">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <?php
		/*
			<p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        	echo $OUTPUT->login_info();
			echo $OUTPUT->home_link();
			echo $OUTPUT->standard_footer_html();
		*/
			$themefootnote = 'theme_'.$PAGE->theme->name.'_footnote_html';
			if ((isset($CFG->$themefootnote)) && ($CFG->$themefootnote != '')) {
				echo $CFG->$themefootnote;
			}
        ?>
	  </div>
	<div>
</footer>
<div id="site-bottom-footer" class="navbar">
	<div id="site-bottom-footer-container" class="container">
	  <div class="row">
		<div id="copyright" class="col-md-3 col-sm-4 col-xs-4"><p class="copy">&copy; 2016 oncampus GmbH</p></div>
		<div id="socialmedia-bar" class="col-md-9 col-sm-8 col-xs-8">
		<ul style="list-style-type:none; float:right;">
			<?php	
			// Social Media Links anzeigen, wenn die URLs dazu in den Theme-Settings angegeben sind
			$cfg_social_links = array('theme_'.$PAGE->theme->name.'_facebook_url' => 'fa-facebook',
									  'theme_'.$PAGE->theme->name.'_googleplus_url' => 'fa-google-plus',
									  'theme_'.$PAGE->theme->name.'_twitter_url' => 'fa-twitter',
									  'theme_'.$PAGE->theme->name.'_youtube_url' => 'fa-youtube',
									  'theme_'.$PAGE->theme->name.'_instagramm_url' => 'fa-instagram',
									  'theme_'.$PAGE->theme->name.'_linkedin_url' => 'fa-linkedin',
									  'theme_'.$PAGE->theme->name.'_xing_url' => 'fa-xing'
									);
			foreach ($cfg_social_links as $key => $value) {
				if ($CFG->$key != '') {
					echo '<li style="display:inline; list-style-type:none;"><a target="blank" href="'.$CFG->$key.'"><i class="fa '.$value.'"></i></a></li>';
				}
			}
			?>
			<li style="display:inline; list-style-type:none;"><a href="#top" class="back-to-top"><i class="fa fa-chevron-up"></i></span></a></li>
		</ul>
		</div>	
	  </div>
	</div>
</div>
</div>



    <?php echo $OUTPUT->standard_end_of_body_html() ?>
	
	<script type='text/javascript' src='<?php echo $CFG->wwwroot;?>/lib/javascript.php/1452530091/lib/jquery/jquery-1.12.1.min.js'></script>
	<script type='text/javascript' src='<?php echo $CFG->wwwroot;?>/theme/ildcustom/javascript/sidebar.js'></script>



</body>
</html>
