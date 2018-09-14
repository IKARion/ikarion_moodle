<?php

class block_webkonf extends block_base {
	public function init() {

		global $USER;
		if (isset($USER->editing)) {
			if ($USER->editing == 1 && !is_siteadmin()) {

			} else {
				$this->title = get_string('blockname', 'block_webkonf');
			}

			$this->title = get_string('blockname', 'block_webkonf');

		}
	}

	public function _self_test() {
		return true;
	}

	public function has_config() {
		return true;
	}

	public function get_content() {
		Global $USER, $DB, $CFG;


		if ($USER->editing == 1 && !is_siteadmin()) {
			# return;
		}

		if ($this->content !== null) {
			return $this->content;
		}
		$id = $USER->id;
		try {
			$id = required_param('id', PARAM_INT);
		}
		catch (Exception $e) {

		}

		$course = $DB->get_record('course', array('id'=>$id));

		$userid="ikarion_".$USER->username;
		$courseid=$course->idnumber;

		if (file_exists("../group/lib.php")) {
			require_once("../group/lib.php");
		} else {
			require_once("../../group/lib.php");

		}
		$EXT_groupedMode 		= false;
		$EXT_userIsGrouped 	= false;
		$EXT_userGroups = array();

		$groups = groups_get_all_groups($course->id);


		if ($course->id != NULL) {

			$context = context_course::instance($course->id, MUST_EXIST);


			if (is_array($groups)) {
				$EXT_groupedMode = true;
				$listed = array();

				$isTeacher = false;
				if (has_capability('moodle/course:manageactivities', $context)) {
					$isTeacher = true;

				}

				# moodle/course:manageactivities
				#  has_capability('moodle/role:switchroles', $context)

				foreach ($groups as $g) {
					$dbg = "\r\n<!-- ".$g->name." -->";
					# echo $dbg;
				}

				foreach ($groups as $g) {
					#echo "<!-- Gruppe '".$g->name."' -->";
					$members = groups_get_members_by_role($g->id, $course->id);

					if ($isTeacher) {
						# echo "Teacher!";
					}

					# echo "<!--";
					# var_dump($members);
					# echo "-->";

					#var_dump($members);echo "\r\n\r\n";
					#echo $g->name." count = ".count($members)."<br>";

# folgende Zeilen
					#$userList = array_shift ( $members );
					#$userList = $userList->users;
# ersetzt durch
					$userList = $members[9];
					if (isset($userList->users)) {
						$userList = $userList->users;
					} else {
						$userList = array('x');
					}

					#echo "<!--";
					#var_dump($userList);
					#echo "-->";

					if ($isTeacher) {
						if (!in_array( $g->name, $listed)) {
							$groupName = $g->name;
							if (strlen($g->name) >= 25) {
								$groupName = substr($g->name,0,25)."...";
							}
							$EXT_userGroups[] = array(
								"groupName" => $groupName,
								"groupUrl"  => $this->groupName2meetingName($course->idnumber."-GRP-".$g->name),
								"titleTag"  => "Den Gruppenraum '".$g->name."' betreten"
							);
							$listed[] = $g->name;
						}
					}

					#	 echo "<pre><tt>".print_r($members)."</tt></pre>";
					foreach ($userList as $m) {
						if ($m == 'x') {
							$m = new Stdclass();
							$m->username = "";
						}
						#echo "<!-- ".$m->username." in ".$g->name."<br> -->";
						if ($USER->username == $m->username || $isTeacher == true ) {
							$EXT_userIsGrouped = true;

							if (!in_array( $g->name, $listed)) {
								$groupName = $g->name;
								if (strlen($g->name) >= 25) {
									$groupName = substr($g->name,0,25)."...";
								}
								$EXT_userGroups[] = array(
									"groupName" => $groupName,
									"groupUrl"  => $this->groupName2meetingName($course->idnumber."-GRP-".$g->name),
									"titleTag"  => "Den Gruppenraum '".$g->name."' betreten"
								);
								$listed[] = $g->name;
							}
						}
					}
				}
			}

		}





		if (($courseid=='')||($courseid==false)) {
			$this->content->text=get_string('cfg_error', 'block_webkonf');
			$this->content->footer='';
		} else {

			if ( empty($this->config->showrecordings)) {

				$recordings = true;
			}else{
				if ($this->config->showrecordings=='yes') {
					$recordings = true;
				} else {
					$recordings = false;
				}

			}

			$helpurl  = (!empty($CFG->block_webkonf_helpurl)) ? $CFG->block_webkonf_helpurl : false;

			$global_helpurl=get_config('webkonf','helpurl');
			$helpurl  = (!empty($global_helpurl)) ? $global_helpurl : false;

			$blockcontent='<div class="webkonfblock_content">';
			$blockcontent.='<a href="'.$CFG->wwwroot.'/blocks/webkonf/room.php?uid='.urlencode($userid).'&room='.urlencode($courseid).'" target="_blank"><img src="'.$CFG->wwwroot.'/blocks/webkonf/pix/webconference.png" border="0"></a>';
			$blockcontent.='<a href="'.$CFG->wwwroot.'/blocks/webkonf/room.php?uid='.urlencode($userid).'&discussion=true&room='.urlencode($courseid).'" target="_blank"><img src="'.$CFG->wwwroot.'/blocks/webkonf/pix/discussion.png" border="0"></a>';




			if ($recordings) {
				$blockcontent.='<a href="'.$CFG->wwwroot.'/blocks/webkonf/recordings.php?id='.$course->id.'"><img src="'.$CFG->wwwroot.'/blocks/webkonf/pix/videoarchiv.png"></a>';
			}
			if ($helpurl) {
				$blockcontent.='<a href="http://oncampuspedia.oncampus.de/loop/Adobe_Connect" target="_blank"><img src="'.$CFG->wwwroot.'/blocks/webkonf/pix/help.png" border="0"></a>';
			}


			if ($EXT_userIsGrouped == true) {

				$blockcontent .= "<div class='webkonfblock_grouplisting'>";

				if (count($EXT_userGroups) == 1) {
					$blockcontent .= "<h3 > 1 Gruppenraum:</h3>";
				}
				if (count($EXT_userGroups) >=2) {
					$blockcontent .= "<h3  >".count($EXT_userGroups)." Gruppenr&auml;ume:</h3>";
				}

				foreach ($EXT_userGroups as $g) {
					$blockcontent .= '
				<a href="'.$CFG->wwwroot.'/blocks/webkonf/room.php?uid='.urlencode($userid).'&c='.$course->id.'&room='.urlencode($g["groupUrl"]).'&grouped=true" target="_blank"
				title="'.$g["titleTag"].'">
				'.$g["groupName"].'</a><br>';
				}
				$blockcontent .= "</div> ";
			}



			$blockcontent.='</div>';
			$this->content = new Stdclass();
			$this->content->text=$blockcontent;
			$this->content->footer='';
		}
		return $this->content;
	}

	function groupName2meetingName($expr) {

		$expr = trim($expr);
		$expr = str_replace(" ","-",$expr);
		#$expr = str_replace(".","-",$expr);
		$expr = str_replace(":","-",$expr);
		$expr = str_replace(";","-",$expr);
		$expr = str_replace("/","-",$expr);
		$expr = str_replace("&uuml;","ü",$expr);

		$expr = str_replace("ä","ae-",$expr);
		$expr = str_replace("ö","oe",$expr);
		$expr = str_replace("ü","ue",$expr);
		$expr = str_replace("Ä","AE",$expr);
		$expr = str_replace("Ü","UE",$expr);
		$expr = str_replace("Ö","OE",$expr);
		$expr = str_replace("ß","SZ",$expr);
		return $expr;
	}


}