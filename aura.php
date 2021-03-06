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




function show_activities() {
  global $USER, $SESSION, $COURSE, $OUTPUT, $CFG;

  $courseid = required_param('id', PARAM_INT);
  $activities = get_course_activities($courseid);
  $students = get_course_students($courseid);
  $studentsCompleted = array();
  $studentsNotCompleted = array();
  $game = new stdClass();
  $game = $SESSION->game;
  $showLetter = $game->config->show_scarlatt_letter;

$activities_html = '
<section id="tabs" class="project-tab">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <nav>
                            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            ';
                            $i = 0;
                            foreach ($activities as $activity) {
                                $name = $activity->name;
                                $quizId = $activity->id;

                                $activities_html .= '
                                    <a class="nav-item nav-link" id="nav-'.$quizId.'-tab"
                                     data-toggle="tab" href="#nav-'.$quizId.'" role="tab"
                                      aria-controls="nav-'.$quizId.'" aria-selected="true">
                                      '.$name.'
                                      </a>';
                                  $i++;
                                }
                                  $activities_html .= '
                                    </div>
                                  </nav>
                                  <div class="tab-content" id="nav-tabContent">';

                                  $j = 0;
                                  foreach ($activities as $activity) {
                                    unset($studentsCompleted);
                                    unset($studentsNotCompleted);
                                    $quizId = $activity->id;
                                    if ($j == 0) {
                                      $activities_html .= '<div class="tab-pane fade show active" id="nav-'.$quizId.'" role="tabpanel" aria-labelledby="nav-'.$quizId.'-tab">
                                      <table class="table" cellspacing="0">
                                      <thead>
                                      <tr>
                                      <th>Aluno</th>
                                      <th>Status</th>
                                      </tr>
                                      </thead>
                                      <tbody>';
                                    }
                                    else {
                                      $activities_html .= '
                                      <div class="tab-pane fade" id="nav-'.$quizId.'" role="tabpanel" aria-labelledby="nav-'.$quizId.'-tab">
                                      <table class="table" cellspacing="0">
                                      <thead>
                                      <tr>
                                      <th>Aluno</th>
                                      <th>Status</th>
                                      </tr>
                                      </thead>
                                      <tbody>';
                                    }
                                    $j++;

                                    $studentsCompleted[] = get_activitie_students($courseid, $quizId);
                                    foreach ($studentsCompleted[0] as $completed) {
                                      $completedId = $completed->id;
                                      $completedName = $completed->nome;
                                      $activities_html .= '<tr>
                                      <td>'.$completedName.'</td>
                                      <td style="color: green" ><i class="fa fa-check"></i></td>
                                      </tr>';
                                    }

                                    $studentsNotCompleted[] = get_not_activitie_students($courseid, $quizId);
                                    if ($showLetter == 1) {
                                      foreach ($studentsNotCompleted[0] as $notCompleted) {
                                        $notCompletedId = $notCompleted->id;
                                        $notCompletedName = $notCompleted->nome;
                                        $activities_html .= '<tr>
                                        <td>'.$notCompletedName.'</td>
                                        <td style="color: red;"><i class="fa fa-times"></i></td>
                                        </tr>';
                                      }
                                    }

                                    $activities_html .=  '
                                    </tr>
                                    </tbody>
                                    </table>
                                    </div>';
                                  }
                        $activities_html .= '
                          </div>
                      </div>
                  </div>
              </section>';
  add_aura_effect_points();
  return $activities_html;
}

function add_aura_effect_points() {
  global $USER, $SESSION, $COURSE, $OUTPUT, $CFG;

  $game = new stdClass();
  $game = $SESSION->game;

  $courseid = required_param('id', PARAM_INT);
  $activities = get_course_activities($courseid);
  $qntdMatriculados = get_qntd_enrols_course($courseid);
  $studentsFromCourse[] = get_course_students($courseid);

$studentsCompleted = array();
$qntdResponderamFormulario  = array();
foreach ($activities as $activity ) {
  unset($qntdResponderamFormulario);
  $quizid = $activity->id;

  $qntdResponderamFormulario [] = get_qntd_respond_course($courseid, $quizid);
  foreach ($qntdResponderamFormulario[0] as $responderam) {
    if ($responderam->count === $qntdMatriculados[$courseid]->qntdmatriculados) {
        foreach ($studentsFromCourse[0] as $student) {
          $studentId = $student->id;
          update_activities_points($courseid, $studentId, $game->config->aura_efect);
        }
    }
  }
}
}
?>
