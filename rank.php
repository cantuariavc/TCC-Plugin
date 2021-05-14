<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/game/lib.php');
require_once($CFG->libdir . '/grouplib.php');

require_login();

global $USER, $SESSION, $COURSE, $OUTPUT, $CFG;

$game = new stdClass();
$game = $SESSION->game;

$courseid = required_param('id', PARAM_INT);

$groupid = optional_param('group', 0, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);


require_login($course);
$context = context_course::instance($courseid);


 function get_rank() {
   global $USER, $CFG;

   $courseid = required_param('id', PARAM_INT);
   $userid = $USER->id;

   $outputhtml .= '<table border="0" width="100%">';
   $rs = rank_list($courseid, $groupid);
   $limit = 0;
   $ord = 1;
   foreach ($rs as $gamer) {
       $avatartxt = '';
       if ($cfggame->use_avatar == 1) {
           $avatartxt = $OUTPUT->pix_icon('a' . get_avatar_user($gamer->userid), 'Avatar', 'block_game');
       }
       $ordtxt = $ord . '&ordm;';
       $usertxt = $avatartxt . ' ******** ';
       if ($game->config->show_identity == 0) {
           $usertxt = $avatartxt . ' ' . $gamer->firstname . ' ' . $gamer->lastname;
       }
       $scoretxt = $gamer->pt;
       if ($gamer->userid == $USER->id) {
           $usertxt = $avatartxt . ' <strong>' . $gamer->firstname . ' ' . $gamer->lastname . '</trong>';
           $scoretxt = '<strong>' . (int) $gamer->pt . '</trong>';
           $ordtxt = '<strong>' . $ord . '&ordm;</trong>';
       }
       $outputhtml .= '<tr>';
       $outputhtml .= '<td>' . $ordtxt . '<hr/></td><td> ' . $usertxt . ' <hr/></td>';
       $outputhtml .= '<td> ' . $scoretxt . '<hr/></td>';
       $outputhtml .= '</tr>';

       if ($limit > 0 && $limit == $ord) {
           break;
       }
       $ord++;
   }
   $outputhtml .= '</table>';

   return $outputhtml;
 }
?>
