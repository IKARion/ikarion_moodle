<?php

function getOCProgress($courseId, $sectionId) {
    global $DB, $CFG, $USER, $SESSION;
    require_once($CFG->libdir . '/gradelib.php');

    if (!$module = $DB->get_record('modules', array('name' => 'hvp'))) {
        return false;
    }

    $cm = $DB->get_records('course_modules', array('section' => $sectionId, 'course' => $courseId, 'module' => $module->id));

    if (count($cm) == 0) {
        return false;
    }

    $percentage = 0;
    $mods_counter = 0;

    foreach ($cm as $mod) {
        if ($mod->visible == 1) {
            $skip = false;

            if (isset($mod->availability)) {
                $availability = json_decode($mod->availability);
                foreach ($availability->c as $criteria) {
                    if ($criteria->type == 'language' && ($criteria->id != $SESSION->lang)) {
                        $skip = true;
                    }
                }
            }

            if (!$skip) {
                $grading_info = grade_get_grades($mod->course, 'mod', 'hvp', $mod->instance, $USER->id);
                $user_grade = $grading_info->items[0]->grades[$USER->id]->grade;

                $percentage += $user_grade;
                $mods_counter++;
            }
        }
    }

    $progress = array('sectionId' => $sectionId, 'percentage' => $percentage / $mods_counter);

    return $progress;


    /*
        $count = count($cm);

        if ($count == 0) {
            return false;
        }

        foreach ($cm as $module) {
            $grading_info = grade_get_grades($module->course, 'mod', 'hvp', $module->instance, $USER->id);
            $user_grade = $grading_info->items[0]->grades[$USER->id]->grade;

            $percentage += $user_grade / $count;
        }

        $progress = array('sectionId' => $sectionId, 'percentage' => $percentage);

        return $progress;
    */
}