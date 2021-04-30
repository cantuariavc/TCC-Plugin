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
 * Game block config form definition
 *
 * @package    block_game
 * @copyright  2019 Jose Wilson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/game/lib.php');
require_once($CFG->libdir . '/completionlib.php');

require_once('calendar.php');
require_once('pet.php');
require_once('aura.php');

require_login();

global $USER, $SESSION, $COURSE, $OUTPUT, $CFG;


$couseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $couseid), '*', MUST_EXIST);

$game = new stdClass();
$game = $SESSION->game;

$showavatar = !isset($game->config->use_avatar) || $game->config->use_avatar == 1;
$showrank = !isset($game->config->show_rank) || $game->config->show_rank == 1;
$showscore = !isset($game->config->show_score) || $game->config->show_score == 1;
$showlevel = !isset($game->config->show_level) || $game->config->show_level == 1;
require_login($course);

$PAGE->set_pagelayout('course');
$PAGE->set_url('/blocks/game/perfil_gamer.php', array('id' => $couseid));
$PAGE->set_context(context_course::instance($couseid));
$PAGE->set_title(get_string('perfil_gamer_title', 'block_game'));
$PAGE->set_heading(get_string('perfil_gamer_title', 'block_game'));


function get_avatar_row($showavatar, $CFG, $game, $OUTPUT, $USER) {
    $avatar_row = '<div class="boxgame">';
    if ($showavatar == 1) {
        $avatar_row .= '<img align="center" hspace="12" src="'.$CFG->wwwroot.'/blocks/game/pix/a'.$game->avatar.'.png" title="avatar"/>';
    } else {
        $avatar_row .= $OUTPUT->user_picture($USER, array('size' => 80, 'hspace' => 12));
    }

    $avatar_row .= '
            <strong>' . $USER->firstname . ' ' . $USER->lastname . '</strong>
        </div>';
    return $avatar_row;
}

function get_rank_row($showrank, $CFG, $game_user) {
    if ($showrank == 1) {
        return '
            <div class="boxgame">
                <img src="'.$CFG->wwwroot.'/blocks/game/pix/big_rank.png" align="center" hspace="12"/>
                <strong>' . get_string('label_rank', 'block_game') . ': ' . $game_user->ranking . '&ordm; / ' . get_players($game_user->courseid) . '</strong>
            </div>';
    } else {
        return '';
    }
}

function get_score_row($showscore, $CFG, $game_user, $fullpoints) {
    if ($showscore == 1) {
        if ($game_user->courseid != 1) {
            return '
                <div class="boxgame">
                    <img src="'.$CFG->wwwroot.'/blocks/game/pix/big_score.png" align="center" hspace="12"/>
                    <strong>' . get_string('label_score', 'block_game') . ': ' . ($game_user->score + $game_user->score_bonus_day +
                    $game_user->score_activities + $game_user->score_section) . '</strong>
                </div>';
        } else {
            return '
                <div class="boxgame">
                    <img src="'.$CFG->wwwroot.'/blocks/game/pix/big_score.png" align="center" hspace="12"/>
                    <strong>' . get_string('label_score', 'block_game') . ': ' . $fullpoints . '</strong>
                </div>';
        }
    } else {
        return '';
    }
}

function get_level_row($showlevel, $CFG, $game_user) {
    if ($showlevel == 1) {
        return '
            <div class="boxgame">
                <img src="'.$CFG->wwwroot.'/blocks/game/pix/big_level.png" align="center" hspace="12"/>
                <strong>' . get_string('label_level', 'block_game') . ': ' . $game_user->level . '</strong>
            </div>';
    } else {
        return '';
    }
}

function get_badges($game, $DB, $CFG) {
    $badges_row = '';
    if ($game->badges != "") {
        $badges = explode(",", $game->badges);
        foreach ($badges as $badge) {
            $coursebadge = $DB->get_record('course', array('id' => $badge));
            $badges_row .= '
                <img src="'.$CFG->wwwroot.'/blocks/game/pix/big_badge.png" align="center" hspace="12"/>
                <strong>' . $coursebadge->fullname . '</strong> ';
        }
    } else {
        $badges_row .= '&emsp;O usuário não possui emblemas.';
    }
    return $badges_row;
}

echo $OUTPUT->header();

$fullpoints = 0;

$outputhtml = '<div class="boxs">';

if ($couseid > 1) {
    $perfil = 'perfil';
    $emblemas = 'emblemas';
    $ranking = 'ranking';
    $bichinho_virtual = 'bichinho-virtual';
    $quests = 'quests';
    $calendario = 'calendario';
    $loja = 'loja';

    $outputhtml = '
        <h3>'.$course->fullname.'</h3><br/>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="'.$perfil.'-tab" data-toggle="tab" href="#'.$perfil.'" role="tab" aria-controls="'.$perfil.'" aria-selected="true">'.ucfirst($perfil).'</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="'.$emblemas.'-tab" data-toggle="tab" href="#'.$emblemas.'" role="tab" aria-controls="'.$emblemas.'" aria-selected="false">'.ucfirst($emblemas).'</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="'.$ranking.'-tab" data-toggle="tab" href="#'.$ranking.'" role="tab" aria-controls="'.$ranking.'" aria-selected="false">'.ucfirst($ranking).'</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="'.$bichinho_virtual.'-tab" data-toggle="tab" href="#'.$bichinho_virtual.'" role="tab" aria-controls="'.$bichinho_virtual.'" aria-selected="false">'.ucwords(str_replace('-', ' ', $bichinho_virtual)).'</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="'.$quests.'-tab" data-toggle="tab" href="#'.$quests.'" role="tab" aria-controls="'.$quests.'" aria-selected="false">'.ucfirst($quests).'</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="'.$calendario.'-tab" data-toggle="tab" href="#'.$calendario.'" role="tab" aria-controls="'.$calendario.'" aria-selected="false">'.ucfirst(substr_replace($calendario, 'ário', 6)).'</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="'.$loja.'-tab" data-toggle="tab" href="#'.$loja.'" role="tab" aria-controls="'.$loja.'" aria-selected="false">'.ucfirst($loja).'</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="'.$perfil.'" role="tabpanel" aria-labelledby="'.$perfil.'-tab">
                <br/>' .
                get_avatar_row($showavatar, $CFG, $game, $OUTPUT, $USER) . '
                <br/>' .
                get_score_row($showscore, $CFG, $game, $fullpoints) . '
                <br/>' .
                get_level_row($showlevel, $CFG, $game) . '
            </div>

            <div class="tab-pane fade" id="'.$emblemas.'" role="tabpanel" aria-labelledby="'.$emblemas.'-tab">' . '
                <br/>'.
                get_badges($game, $DB, $CFG).'
            </div>

            <div class="tab-pane fade" id="'.$ranking.'" role="tabpanel" aria-labelledby="'.$ranking.'-tab">' . '
                <br/>'.
                get_rank_row($showrank, $CFG, $game) .'
            </div>

            <div class="tab-pane fade" id="'.$bichinho_virtual.'" role="tabpanel" aria-labelledby="'.$bichinho_virtual.'-tab">
                <br/>'.
                get_pet().'
            </div>

            <div class="tab-pane fade" id="'.$quests.'" role="tabpanel" aria-labelledby="'.$quests.'-tab"><br/>' .
              show_activities() .'
            </div>

            <div class="tab-pane fade" id="'.$calendario.'" role="tabpanel" aria-labelledby="'.$calendario.'-tab"> 
                <br/>'.
                get_calendar().'
            </div>

            <div class="tab-pane fade" id="'.$loja.'" role="tabpanel" aria-labelledby="'.$loja.'-tab"><br/>&emsp;Loja</div>
        </div>';
} elseif ($couseid == 1) {
    $outputhtml .= '<div class="boxgame">';

    if ($showavatar == 1) {
        $outputhtml .= '<img  align="center" hspace="12" src="'.$CFG->wwwroot.'/blocks/game/pix/a'.$game->avatar.'.png" title="avatar"/>';
    } else {
        $outputhtml .= $OUTPUT->user_picture($USER, array('size' => 80, 'hspace' => 12));
    }

    $outputhtml .= '
            <strong>'.$USER->firstname.' '.$USER->lastname.'</strong>
        </div>
        <hr/>';
    $rs = get_games_user($USER->id);

    foreach ($rs as $gameuser) {
        $fullpoints = ($fullpoints + ($gameuser->score + $gameuser->score_bonus_day + $gameuser->score_activities + $gameuser->score_badges +
            $gameuser->score_section));
        $course = $DB->get_record('course', array('id' => $gameuser->courseid));

        $outputhtml .= '<h3>';
        if ($gameuser->courseid != 1) {
            $outputhtml .= $course->fullname;
        } else {
            $outputhtml .= get_string('general', 'block_game');
        }
        $outputhtml .= '
            </h3><br/>
            <div class="boxgame">' .
                get_rank_row($showrank, $CFG, $gameuser) . '
                <br/>' .
                get_score_row($showscore, $CFG, $gameuser, $fullpoints) . '
                <br/>' .
                get_level_row($showlevel, $CFG, $game_user) . '
            </div>
            <hr/>
            &emsp;<h3>Emblemas</h3>' .
            get_badges($game, $DB, $CFG);
    }
}
$outputhtml .= '</div>';

echo $outputhtml;
echo $OUTPUT->footer();
