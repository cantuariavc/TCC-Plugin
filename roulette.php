<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/game/lib.php');
require_once($CFG->libdir . '/completionlib.php');

require_login();

global $USER, $SESSION, $COURSE, $OUTPUT, $CFG;

$confirm = optional_param('c', 0, PARAM_INT);
$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$game = new stdClass();
$game = $SESSION->game;

require_login($course);

$PAGE->set_pagelayout('course');
$PAGE->set_url('/blocks/game/roulette.php', array('id' => $courseid));
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_title(get_string('roulette_title', 'block_game'));
$PAGE->set_heading(get_string('roulette_title', 'block_game'));

echo $OUTPUT->header();
$outputhtml = '<div class="boxs">';

if ($courseid > 1) {
    $context = context_course::instance($courseid, MUST_EXIST);
    if (has_capability('moodle/course:update', $context, $USER->id)) {
        $students = get_students_lastaccess($courseid);

        $outputhtml .= '
            <div align="center">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($students as $student) {
            $name = $student->nome;
            $status = ($student->lastaccess >= time() - 900) ? "on-line" : "off-line";
            
            $outputhtml .= '
                <tr>
                    <td>'.$name.'</td>
                    <td>'.$status.'</td>
                </tr>';
        }
        
        $outputhtml .= '
                </tbody>
            </table>';
    } else {
        $outputhtml .= '<strong>' . get_string('roulette_not_permission', 'block_game') . '</strong><br/>';
    }
}
$outputhtml .= '</div>';
echo $outputhtml;
echo $OUTPUT->footer();

?>