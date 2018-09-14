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
 * Question controller
 *
 * @package mod_groupformation
 * @author Eduard Gallwas, Johannes Konert, Rene Roepke, Nora Wester, Ahmed Zukic
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
if (!defined('MOODLE_INTERNAL')) {
    die ('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/likert_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/topic_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/basic_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/range_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/knowledge_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/dropdown_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/freetext_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/multiselect_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/number_question.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/questionnaire/question_table.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/moodle_interface/user_manager.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/moodle_interface/storage_manager.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/util/util.php');
require_once($CFG->dirroot . '/mod/groupformation/classes/util/define_file.php');
require_once($CFG->dirroot . '/mod/groupformation/locallib.php');

class mod_groupformation_questionnaire_controller {

    /** @var int The id of the groupformation activity */
    private $groupformationid = null;

    /** @var mod_groupformation_storage_manager instance of storage manager */
    private $store = null;

    /** @var mod_groupformation_user_manager The manager of user data */
    private $usermanager = null;

    /** @var int ID of user */
    public $userid;

    /** @var int The id of the course module */
    private $cmid;

    /** @var bool Flag for highlighting missing answers */
    private $highlightmissinganswers = false;

    /** @var int Position of category */
    private $categoryposition = 0;

    /** @var string Current category */
    private $category;

    /** @var array Categories of questionnaire */
    private $categories = array();

    /**
     * mod_groupformation_questionnaire_controller constructor.
     *
     * @param $groupformationid
     * @param $userid
     * @param $oldcategory
     * @param $cmid
     * @internal param $lang
     */
    public function __construct($groupformationid, $userid, $oldcategory, $cmid) {
        $this->groupformationid = $groupformationid;
        $this->userid = $userid;
        $this->cmid = $cmid;

        $this->store = new mod_groupformation_storage_manager ($groupformationid);
        $this->usermanager = new mod_groupformation_user_manager ($groupformationid);

        $this->categories = $this->store->get_categories();
        $this->set_internal_number($oldcategory);
    }

    /**
     * Triggers going a category page back
     */
    public function go_back() {
        $this->categoryposition = max($this->categoryposition - 2, 0);
    }

    /**
     * Regulates not going on and highlighting missing answers
     */
    public function not_go_on() {
        $this->categoryposition = max($this->categoryposition - 1, 0);
        $this->highlightmissinganswers = true;
    }

    /**
     * Returns percent of progress in questionnaire
     *
     * @param string $category
     * @return number
     */
    public function get_percent($category = null) {
        if (!is_null($category)) {
            $categories = $this->store->get_categories();
            $pos = array_search($category, $categories);
            return 100.0 * ((1.0 * $pos) / count($categories));
        }

        $total = 0;
        $sub = 0;
        $temp = 0;

        $numbers = $this->store->get_numbers($this->categories);

        foreach ($numbers as $num) {
            if ($num != 0) {
                $total++;
                if ($temp < $this->categoryposition) {
                    $sub++;
                }
            }
            $temp++;
        }

        return ($sub / $total) * 100;
    }

    /**
     * Sets internal page number
     *
     * @param unknown $category
     */
    private function set_internal_number($category) {
        if ($category != "") {
            $this->categoryposition = $this->store->get_position($category) + 1;
        }
    }

    /**
     * Returns whether there is a next category or not
     *
     * @return boolean
     */
    public function has_next() {
        return ($this->categoryposition != -1 && $this->categoryposition < count($this->categories));
    }

    /**
     * Returns question in current language or possible default language
     *
     * @param int $i
     * @param int $version
     * @return stdClass
     */
    public function get_question($i, $version) {
        $category = $this->category;
        $lang = get_string('language', 'groupformation');

        $record = $this->store->get_catalog_question($i, $category, $lang, $version);

        if (empty ($record)) {
            if ($lang != 'en') {
                $record = $this->store->get_catalog_question($i, $category, $lang, $version);
            } else {
                $lang = $this->store->get_possible_language($category);
                $record = $this->store->get_catalog_question($i, $category, $lang, $version);
            }
        }

        return $record;
    }

    /**
     * Returns whether current category is 'topic' or not
     *
     * @return boolean
     */
    public function is_topics() {
        return $this->categoryposition == $this->store->get_position('topic');
    }

    /**
     * Returns whether current category is 'knowledge' or not
     *
     * @return boolean
     */
    public function is_knowledge() {
        return $this->categoryposition == $this->store->get_position('knowledge');
    }

    /**
     * Returns whether current category is 'points' or not
     *
     * @return boolean
     */
    public function is_points() {
        return $this->categoryposition == $this->store->get_position('points');
    }

    /**
     * Returns questions
     *
     * @return array
     */
    public function get_next_questions() {
        $category = $this->category;
        if ($this->categoryposition != -1) {

            $questions = array();

            $hasanswer = $this->usermanager->has_answers($this->userid, $category);

            if ($this->is_knowledge() || $this->is_topics()) {
                $temp = $this->store->get_knowledge_or_topic_values($category);
                $xmlcontent = '<?xml version="1.0" encoding="UTF-8" ?> <OPTIONS> ' . $temp . ' </OPTIONS>';
                $values = mod_groupformation_util::xml_to_array($xmlcontent);

                $options = array(
                    100 => get_string('excellent', 'groupformation'), 0 => get_string('none', 'groupformation'));

                $position = 1;
                $questionsfirst = array();
                $answerposition = array();

                $i = 1;
                foreach ($values as $value) {

                    if ($hasanswer) {
                        $answer = $this->usermanager->get_single_answer(
                            $this->userid, $category, $position);
                        if ($answer == false) {
                            $answer = -1;
                        }
                        $answerposition[$answer] = $position - 1;
                        $position++;
                    } else {
                        $answer = -1;
                    }

                    $questionid = $i;
                    $question = $value;

                    $name = 'mod_groupformation_' . $category . '_question';
                    $questionobj = new $name($category, $questionid, $question, $options, $answer);

                    $questionsfirst[] = $questionobj;
                    $i++;
                }

                $l = count($answerposition);

                if ($l > 0 && $this->categoryposition == $this->store->get_position('topic')) {
                    // Topics are rated by users as: the topmost = most wanted=rating value highest number.
                    // Therefore here we sort them accordingly top downwards by rating.
                    for ($k = $l; $k >= 1; $k--) {
                        $h = $questionsfirst[$answerposition[$k]];
                        $h->set_answer($answerposition[$k]);
                        $questions[] = $h;
                    }
                } else {
                    $questions = $questionsfirst;
                }
            } else if ($this->is_points()) {

                $records = $this->store->get_questions_randomized_for_user($category, $this->userid);

                foreach ($records as $record) {

                    $type = $record->type;

                    $options = array(
                        $this->store->get_max_points() => get_string('excellent', 'groupformation'),
                        0 => get_string('bad', 'groupformation'));

                    $answer = $this->usermanager->get_single_answer($this->userid, $category, $record->questionid);
                    if ($answer == false) {
                        $answer = -1;
                    }

                    $questionid = $record->questionid;
                    $question = $record->question;

                    $name = 'mod_groupformation_' . $type . '_question';
                    $questionobj = new $name($category, $questionid, $question, $options, $answer);

                    $questions [] = $questionobj;
                }

            } else {

                $records = $this->store->get_questions_randomized_for_user($category, $this->userid);

                foreach ($records as $record) {

                    $type = $record->type;
                    $questionid = $record->questionid;
                    $question = $record->question;

                    $temp = '<?xml version="1.0" encoding="UTF-8" ?> <OPTIONS> ' . $record->options . ' </OPTIONS>';
                    $options = mod_groupformation_util::xml_to_array($temp);

                    $answer = $this->usermanager->get_single_answer($this->userid, $category, $record->questionid);

                    $name = 'mod_groupformation_' . $type . '_question';
                    $questionobj = new $name($category, $questionid, $question, $options, $answer);

                    $questions [] = $questionobj;
                }

            }

            return $questions;
        }

    }

    /**
     * Prints action buttons for questionnaire page
     */
    public function print_action_buttons() {
        echo '<div class="grid">';
        echo '    <div class="col_m_100 questionaire_button_row">';
        echo '        <button type="submit" name="direction" value="0" class="gf_button gf_button_pill gf_button_small">';
        echo get_string('previous');
        echo '        </button>';
        echo '        <button type="submit" name="direction" value="1" class="gf_button gf_button_pill gf_button_small">';
        echo get_string('next');
        echo '        </button>';
        echo '    </div>';
        echo '</div>';
    }

    /**
     * Prints navigation bar
     *
     * @param string $activecategory
     */
    public function print_navbar($activecategory = null) {
        $context = context_module::instance($this->cmid);

        $tempcategories = $this->store->get_categories();

        $categories = array();

        foreach ($tempcategories as $category) {

            if ($this->store->get_number($category) > 0) {
                $categories [] = $category;
            }

        }

        echo '<div id="questionaire_navbar">';
        echo '<ul id="accordion">';
        $prevcomplete = !$this->store->all_answers_required();

        foreach ($categories as $category) {

            $url = new moodle_url ('questionnaire_view.php', array(
                'id' => $this->cmid, 'category' => $category));
            $positionactivecategory = $this->store->get_position($activecategory);
            $positioncategory = $this->store->get_position($category);

            $beforeactive = ($positioncategory <= $positionactivecategory);
            $class = 'no-active';
            if (has_capability('mod/groupformation:editsettings', $context) || $beforeactive || $prevcomplete) {
                $class = '';
            }

            $current = ($activecategory == $category) ? 'current' : 'accord_li';

            echo '<li class="' . $current . '">';
            echo '<a class="' . $class . '"  href="' . $url . '">';
            echo '<span>';
            echo $positioncategory + 1;
            echo '</span>';
            echo get_string('category_' . $category, 'groupformation');
            echo '</a>';
            echo '</li>';

        }

        echo '</ul>';
        echo '</div>';
    }

    /**
     * Prints final page of questionnaire
     */
    public function print_final_page() {
        $context = context_module::instance($this->cmid);

        echo '<div class="col_m_100">';
        echo '<h4>';
        echo get_string('questionnaire_no_more_questions', 'groupformation');
        echo '</h>';
        echo '</div>';
        $action = htmlspecialchars($_SERVER ["PHP_SELF"]);
        echo '<form action="' . $action . '" method="post" autocomplete="off" class="groupformation_questionnaire">';

        echo '<input type="hidden" name="category" value="no"/>';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

        $activityid = optional_param('id', $this->groupformationid, PARAM_INT);
        echo '<input type="hidden" name="id" value="' . $activityid . '"/>';

        if (has_capability('mod/groupformation:editsettings', $context)) {
            echo '<div class="alert col_m_100 questionaire_hint">' .
                get_string('questionnaire_submit_disabled_teacher', 'groupformation') . '</div>';
        }

        $url = new moodle_url ('/mod/groupformation/view.php', array(
            'id' => $this->cmid, 'do_show' => 'view'));

        echo '<div class="grid">';
        echo '    <div class="questionaire_button_text">';
        echo '        <div class="col_m_100 questionaire_button_row">';
        echo '            <a href=' . $url->out() . '>';
        echo '                <span class="gf_button gf_button_pill gf_button_small">';
        echo get_string('questionnaire_go_to_start', 'groupformation');
        echo '                </span>';
        echo '            </a>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';

        echo '</form>';
    }

    /**
     * Prints questionnaire page
     */
    public function render() {
        $context = context_module::instance($this->cmid);

        if (groupformation_get_current_questionnaire_version() > $this->store->get_version()) {
            echo '<div class="alert">' . get_string('questionnaire_outdated', 'groupformation') . '</div>';
        }

        if ($this->has_next()) {
            $category = $this->categories[$this->categoryposition];
            $this->category = $category;

            $isteacher = has_capability('mod/groupformation:editsettings', $context);

            if ($isteacher) {
                echo '<div class="alert">' . get_string('questionnaire_preview', 'groupformation') . '</div>';
            }

            if ($this->usermanager->is_completed($this->userid) || !$this->store->is_questionnaire_available()) {
                echo '<div class="alert" id="commited_view">';
                echo get_string('questionnaire_committed', 'groupformation');
                echo '</div>';
            }

            $percent = $this->get_percent($category);

            if (mod_groupformation_data::ask_for_participant_code() && !$isteacher) {
                $this->print_participant_code();
            }

            $this->print_navbar($category);

            $this->print_progressbar($percent);

            $questions = $this->get_next_questions();

            $this->print_questions($questions, $percent);

        } else {

            if ($this->usermanager->has_answered_everything($this->userid)) {
                $this->usermanager->set_evaluation_values($this->userid);
            }

            $returnurl = new moodle_url ('/mod/groupformation/analysis_view.php', array(
                    'id' => $this->cmid, 'do_show' => 'analysis'));
            redirect($returnurl);
        }
    }

    /**
     * Prints table with questions
     *
     * @param array $questions
     * @param unknown $percent
     */
    public function print_questions($questions, $percent) {

        echo '<form style="width:100%; float:left;" action="';
        echo htmlspecialchars($_SERVER ["PHP_SELF"]);
        echo '" method="post" autocomplete="off" class="groupformation_questionnaire">';

        if (!is_null($questions) && count($questions) != 0) {

            $category = $this->category;
            var_dump($category);
            $table = new mod_groupformation_question_table ($category);

            // Here is the actual category and groupformationid is sent hidden.
            echo '<input type="hidden" name="category" value="' . $category . '"/>';

            echo '<input type="hidden" name="percent" value="' . $percent . '"/>';

            echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

            $activityid = optional_param('id', $this->groupformationid, PARAM_INT);

            echo '<input type="hidden" name="id" value="' . $activityid . '"/>';

            echo '<h4 class="view_on_mobile">';
            echo get_string('category_' . $category, 'groupformation');
            echo '</h4>';

            // Print the header of a table or unordered list.
            $table->print_header();

            foreach ($questions as $q) {

                $q->print_html($this->highlightmissinganswers, $this->store->all_answers_required());

            }

            // Print the footer of a table or unordered list.
            $table->print_footer();
        }

        $this->print_action_buttons();

        echo '</form>';
    }

    /**
     * Prints progress bar
     *
     * @param unknown $percent
     */
    public function print_progressbar($percent) {
        echo '<div class="progress">';

        echo '    <div class="questionaire_progress-bar" role="progressbar" aria-valuenow="' . $percent .
            '" aria-valuemin="0" aria-valuemax="100" style="width:' . $percent . '%">';
        echo '    </div>';

        echo '</div>';
    }

    /**
     * Prints participant code for user
     */
    public function print_participant_code() {
        echo '<div class="participantcode">';

        echo get_string('participant_code_footer', 'groupformation');
        echo ': ';
        echo $this->usermanager->get_participant_code($this->userid);

        echo '</div>';
    }

    /**
     * Saves answers for user
     *
     * @param $category
     * @return bool
     */
    public function save_answers($category) {
        $go = true;
        $number = $this->store->get_number($category);

        if ($category == 'knowledge' || $category == 'topic') {
            for ($i = 1; $i <= $number; $i++) {
                $question = new stdClass();
                $question->type = $category;
                $question->questionid = $i;
                $this->handle_answer($this->userid, $category, $question);
            }
        } else {
            $questions = $this->store->get_questions_randomized_for_user($category, $this->userid);

            foreach ($questions as $question) {
                $this->handle_answer($this->userid, $category, $question);
            }
        }

        if ($this->store->all_answers_required() && $this->usermanager->get_number_of_answers(
                $this->userid, $category) != $number) {
            $go = false;
        }

        return $go;
    }

    /**
     * Handles answers
     *
     * @param $userid
     * @param $category
     * @param $question
     */
    public function handle_answer($userid, $category, $question) {

        $type = $question->type;
        $questionid = $question->questionid;
        $name = 'mod_groupformation_' . $type . '_question';
        $questionobj = new $name($category, $questionid);
        $answer = $questionobj->read_answer();
        if (is_null($answer)) {
            return;
        }

        if ($answer[0] == "save") {
            $this->usermanager->save_answer($userid, $category, $answer[1], $questionid);
        } else if ($answer[0] == "delete") {
            $this->usermanager->delete_answer($userid, $category, $questionid);
        }
    }
}