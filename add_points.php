<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/game/lib.php');
require_once($CFG->libdir . '/completionlib.php');

require_login();

global $USER, $SESSION, $COURSE, $OUTPUT, $CFG;

$confirm = optional_param('c', 0, PARAM_INT);
$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$studentId = required_param('studentId', PARAM_INT);

$game = new stdClass();
$game = $SESSION->game;

require_login($course);

$PAGE->set_pagelayout('course');
$PAGE->set_url('/blocks/game/add_points.php', array('id' => $courseid));
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_title(get_string('add_points_title', 'block_game'));
$PAGE->set_heading(get_string('add_points_title', 'block_game'));

echo $OUTPUT->header();
$outputhtml = '<div class="boxs">';

if ($courseid > 1) {
    $context = context_course::instance($courseid, MUST_EXIST);
    if (has_capability('moodle/course:update', $context, $USER->id)) {
        $outputhtml .= '<div align="center">';

        $outputhtml .= '<h3>( ' . get_string('add_points_title', 'block_game') . ': <strong>'
                . $course->fullname . '</strong> )</h3><br/>';

        $outputhtml .= '<br/><h5>';
        if ($confirm > 0) {
            if (isset($game->config->bonus_day)) {
                $add_bonus_day_points = $game->score_bonus_day + $game->config->bonus_day;
            } else {
                $add_bonus_day_points = $game->score_bonus_day + 20;
            }

            if (update_points($courseid, $studentId, $add_bonus_day_points)) {
                $outputhtml .= '<strong>' . get_string('add_points_sucess', 'block_game')
                        . '</strong><br/><br/><a class="btn btn-success" href="' . $CFG->wwwroot . '/course/view.php?id='
                        . $courseid . '">' . get_string('ok', 'block_game') . '</a>';
            } else {
                $outputhtml .= '<strong>' . get_string('add_points_error', 'block_game') . '</strong><br/>';
            }
        } else {
            $outputhtml .= '<strong>' . get_string('label_confirm_add_points', 'block_game') . '</strong><br/><br/>';
            $outputhtml .= '<a class="btn btn-secondary" href="' . $CFG->wwwroot . '/course/view.php?id=' . $courseid . '">'
                    . get_string('no', 'block_game') . '</a>' . '  <a class="btn btn-success" href="add_points.php?id='
                    . $courseid . '&studentId='.$studentId.'&c=1">' . get_string('yes', 'block_game') . '</a>' . '<br/>';
        }
        $outputhtml .= '</h5>';
        $outputhtml .= '</div>';
    } else {
        $outputhtml .= '<strong>' . get_string('add_points_not_permission', 'block_game') . '</strong><br/>';
    }
}
$outputhtml .= '</div>';
echo $outputhtml;
echo $OUTPUT->footer();
