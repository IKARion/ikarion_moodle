<?php

//  Display the course home page.

    require_once('../../config.php');
	require_once($CFG->dirroot.'/course/lib.php');    
   # require_once($CFG->dirroot.'/mod/forum/lib.php');
   # require_once($CFG->libdir.'/conditionlib.php');
   # require_once($CFG->libdir.'/completionlib.php');
	require_once($CFG->dirroot.'/blocks/webkonf/connect_lib.php');    

    $id          = optional_param('id', 0, PARAM_INT);
    $name        = optional_param('name', '', PARAM_RAW);
    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $hide        = optional_param('hide', 0, PARAM_INT);
    $show        = optional_param('show', 0, PARAM_INT);
    $idnumber    = optional_param('idnumber', '', PARAM_RAW);
    $sectionid   = optional_param('sectionid', 0, PARAM_INT);
    $section     = optional_param('section', 0, PARAM_INT);
    $move        = optional_param('move', 0, PARAM_INT);
    $marker      = optional_param('marker',-1 , PARAM_INT);
    $switchrole  = optional_param('switchrole',-1, PARAM_INT);
    $modchooser  = optional_param('modchooser', -1, PARAM_BOOL);

    $params = array();
    if (!empty($name)) {
        $params = array('shortname' => $name);
    } else if (!empty($idnumber)) {
        $params = array('idnumber' => $idnumber);
    } else if (!empty($id)) {
        $params = array('id' => $id);
    }else {
        print_error('unspecifycourseid', 'error');
    }

    $course = $DB->get_record('course', $params, '*', MUST_EXIST);

    $urlparams = array('id' => $course->id);

    // Sectionid should get priority over section number
    if ($sectionid) {
        $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid, 'course' => $course->id), MUST_EXIST);
    }
    if ($section) {
        $urlparams['section'] = $section;
    }
  
  
    $PAGE->set_url('/course/view.php', $urlparams); // Defined here to avoid notices on errors etc

    // Prevent caching of this page to stop confusion when changing page after making AJAX changes
    $PAGE->set_cacheable(false);

    #preload_course_contexts($course->id);
 
     context_helper::preload_course($course->id) ;

    $context = context_course::instance($course->id, MUST_EXIST);

    // Remove any switched roles before checking login
    if ($switchrole == 0 && confirm_sesskey()) {
        role_switch($switchrole, $context);
    }

    require_login($course);

    // Switchrole - sanity check in cost-order...
    $reset_user_allowed_editing = false;
    if ($switchrole > 0 && confirm_sesskey() &&
        has_capability('moodle/role:switchroles', $context)) {
        // is this role assignable in this context?
        // inquiring minds want to know...
        $aroles = get_switchable_roles($context);
        if (is_array($aroles) && isset($aroles[$switchrole])) {
            role_switch($switchrole, $context);
            // Double check that this role is allowed here
            require_login($course);
        }
        // reset course page state - this prevents some weird problems ;-)
        $USER->activitycopy = false;
        $USER->activitycopycourse = NULL;
        unset($USER->activitycopyname);
        unset($SESSION->modform);
        $USER->editing = 0;
        $reset_user_allowed_editing = true;
    }

    //If course is hosted on an external server, redirect to corresponding
    //url with appropriate authentication attached as parameter
    if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
        include $CFG->dirroot .'/course/externservercourse.php';
        if (function_exists('extern_server_course')) {
            if ($extern_url = extern_server_course($course)) {
                redirect($extern_url);
            }
        }
    }


    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    $logparam = 'id='. $course->id;
    $loglabel = 'view';
    $infoid = $course->id;
    if(!empty($section)) {
        $loglabel = 'view section';
        $sectionparams = array('course' => $course->id, 'section' => $section);
        $coursesections = $DB->get_record('course_sections', $sectionparams, 'id', MUST_EXIST);
        $infoid = $coursesections->id;
        $logparam .= '&sectionid='. $infoid;
    }
    add_to_log($course->id, 'course', $loglabel, "view.php?". $logparam, $infoid);

    $course->format = clean_param($course->format, PARAM_ALPHA);
    if (!file_exists($CFG->dirroot.'/course/format/'.$course->format.'/format.php')) {
        $course->format = 'weeks';  // Default format is weeks
    }

    $PAGE->set_pagelayout('course');
    $PAGE->set_pagetype('course-view-' . $course->format);
    $PAGE->set_other_editing_capability('moodle/course:manageactivities');

    if ($reset_user_allowed_editing) {
        // ugly hack
        unset($PAGE->_user_allowed_editing);
    }

    if (!isset($USER->editing)) {
        $USER->editing = 0;
    }
    if ($PAGE->user_allowed_editing()) {
        if (($edit == 1) and confirm_sesskey()) {
            $USER->editing = 1;
            // Redirect to site root if Editing is toggled on frontpage
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot .'/?redirect=0');
            } else {
                $url = new moodle_url($PAGE->url, array('notifyeditingon' => 1));
                redirect($url);
            }
        } else if (($edit == 0) and confirm_sesskey()) {
            $USER->editing = 0;
            if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
                $USER->activitycopy       = false;
                $USER->activitycopycourse = NULL;
            }
            // Redirect to site root if Editing is toggled on frontpage
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot .'/?redirect=0');
            } else {
                redirect($PAGE->url);
            }
        }
        if (($modchooser == 1) && confirm_sesskey()) {
            set_user_preference('usemodchooser', $modchooser);
        } else if (($modchooser == 0) && confirm_sesskey()) {
            set_user_preference('usemodchooser', $modchooser);
        }

        if (has_capability('moodle/course:update', $context)) {
            if ($hide && confirm_sesskey()) {
                set_section_visible($course->id, $hide, '0');
                redirect($PAGE->url);
            }

            if ($show && confirm_sesskey()) {
                set_section_visible($course->id, $show, '1');
                redirect($PAGE->url);
            }

            if (!empty($section)) {
                if (!empty($move) and confirm_sesskey()) {
                    $destsection = $section + $move;
                    if (move_section_to($course, $section, $destsection)) {
                        // Rebuild course cache, after moving section
                        rebuild_course_cache($course->id, true);
                        if ($course->id == SITEID) {
                            redirect($CFG->wwwroot . '/?redirect=0');
                        } else {
                            redirect(course_get_url($course));
                        }
                    } else {
                        echo $OUTPUT->notification('An error occurred while moving a section');
                    }
                }
            }
        }
    } else {
        $USER->editing = 0;
    }

    $SESSION->fromdiscussion = $PAGE->url->out(false);


    if ($course->id == SITEID) {
        // This course is not a real course.
        redirect($CFG->wwwroot .'/');
    }

    $completion = new completion_info($course);
    if ($completion->is_enabled()  ) {
        $PAGE->requires->string_for_js('completion-title-manual-y', 'completion');
        $PAGE->requires->string_for_js('completion-title-manual-n', 'completion');
        $PAGE->requires->string_for_js('completion-alt-manual-y', 'completion');
        $PAGE->requires->string_for_js('completion-alt-manual-n', 'completion');

        $PAGE->requires->js_init_call('M.core_completion.init');
    }

    // We are currently keeping the button here from 1.x to help new teachers figure out
    // what to do, even though the link also appears in the course admin block.  It also
    // means you can back out of a situation where you removed the admin block. :)
    if ($PAGE->user_allowed_editing()) {
        $buttons = $OUTPUT->edit_button($PAGE->url);
        $PAGE->set_button($buttons);
    }

    $PAGE->set_title(get_string('course') . ': ' . $course->fullname);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    if ($completion->is_enabled()  ) {
        // This value tracks whether there has been a dynamic change to the page.
        // It is used so that if a user does this - (a) set some tickmarks, (b)
        // go to another page, (c) clicks Back button - the page will
        // automatically reload. Otherwise it would start with the wrong tick
        // values.
        echo html_writer::start_tag('form', array('action'=>'.', 'method'=>'get'));
        echo html_writer::start_tag('div');
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'completion_dynamic_change', 'name'=>'completion_dynamic_change', 'value'=>'0'));
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('form');
    }

    // Course wrapper start.
    echo html_writer::start_tag('div', array('class'=>'course-content webkonf'));


$global_connect_username=get_config('webkonf','username');
$connect_username  = (!empty($global_connect_username)) ? $global_connect_username : false;

$global_connect_pass=get_config('webkonf','pass');
$connect_pass  = (!empty($global_connect_pass)) ? $global_connect_pass : false;

$global_connect_host=get_config('webkonf','host');
$connect_host  = (!empty($global_connect_host)) ? $global_connect_host : false;

$global_connect_port=get_config('webkonf','port');
$connect_port  = (!empty($global_connect_port)) ? $global_connect_port : false;

$global_connect_folderid=get_config('webkonf','folderid');
$connect_folderid  = (!empty($global_connect_folderid)) ? $global_connect_folderid : false;


if ((!$connect_username)||(!$connect_pass)||(!$connect_host)||(!$connect_port)||(!$connect_folderid)) {
	error(get_string('cfg_error', 'block_webkonf'));
}


$connect = new AC_controler("connect.oncampus.de",443,$connect_folderid,$connect_username,$connect_pass,true);    
$recordings=$connect->getRecordings($course->idnumber);

// if ($USER->username == 'riegejan') {
	// print_object($recordings);
// }


# --------------------------------------------------------------------------------------------------------------------------------
# �nderung: User die bisher noch nie im Hauptmeeting waren sollen nun auch auf die Aufzeichnungen zugreifen k�nnen:
$connect_user_id=$connect->lookupUser($USER->username); 
$room = $course->idnumber;

if (!$connect_user_id) {
	$connect->createUser($USER->username, $USER->firstname, $USER->lastname);
}
if ($discussion == true) {
	$connect->unassignUser ($USER->username, $room);
}

if (!$connect->checkAssignment($USER->username, $room)) {
	$connect->assignUser ($USER->username, $room, 1);
}
# --------------------------------------------------------------------------------------------------------------------------------


$recordingsname =get_string('recordingsname','block_webkonf');
$recordname = get_string('recordname','block_webkonf');
$recorddescription = get_string('recorddescription','block_webkonf');
$recorddate = get_string('recorddate','block_webkonf');
$recordduration = get_string('recordduration','block_webkonf');
echo "<br/><h2 class='main'>".$recordingsname."</h2><br/>";
echo "<br/><table class='groupinfobox generaltable'>";
echo "<tr><th class='header'> ".$recordname." </th><th class='header'> ".$recorddescription." </th><th class='header'> ".$recorddate." </th><th class='header'> ".$recordduration." </th></tr>";

foreach ($recordings as $recording)
	{
	echo "<tr>";
	echo "<td><a href='".$CFG->wwwroot."/blocks/webkonf/recording.php?uid=".urlencode($USER->username)."&recurl=".urlencode($recording['url'])."' target='_blank'>".$recording['name']."</a></td>";
	echo "<td>".$recording['description']."</td>";
	echo "<td>".$recording['date']."</td>";
	echo "<td>".$recording['length']."</td>";
	echo "</tr>";
	}
echo "</table>";        
    
    echo html_writer::end_tag('div');

    // Include course AJAX
    if (include_course_ajax($course, $modnamesused)) {
        // Add the module chooser
        $renderer = $PAGE->get_renderer('core', 'course');
        echo $renderer->course_modchooser(get_module_metadata($course, $modnames, $displaysection), $course);
    }

    echo $OUTPUT->footer();
