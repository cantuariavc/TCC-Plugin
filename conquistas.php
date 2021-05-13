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




function show_achievments() {

  $courseid = required_param('id', PARAM_INT);
  $activities = get_course_activities($courseid);
  $students = get_course_students($courseid);

$achievments_html = '
<div class="container">
	<div class="row">'
    .get_pet_life().
		'<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="offer offer-success">
				<div class="shape" style="">
					<div class="shape-text">
						Daily
					</div>
				</div>
				<div class="offer-content">
					<h3 class="lead">
						Rei da presença
					</h3>
					<p>
						Você mora aqui ? Porque sua presença é perfeita! Já fazem mais de 30 dias que você não falta!
					</p>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="offer offer-warning">
				<div class="shape" style="">
					<div class="shape-text">
						Daily
					</div>
				</div>
				<div class="offer-content">
					<h3 class="lead">
						Turista
					</h3>
					<p>
						Sua presença é sempre muito importante nesse curso, seria ótimo ter você mais dias conosco
					</p>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="offer offer-radius offer-danger">
				<div class="shape" style="">
					<div class="shape-text">
						Daily
					</div>
				</div>
				<div class="offer-content">
					<h3 class="lead">
						Você é dessa turma ?
					</h3>
					<p>
						Se sim, seria bom que você aparecesse mais, sua ausência pode acabar  te prejudicando.
					</p>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="offer offer-success">
				<div class="shape" style="">
					<div class="shape-text">
						Quests
					</div>
				</div>
				<div class="offer-content">
					<h3 class="lead">
						Desbravador do Moodle
					</h3>
					<p>
						Você completou todas as atividades desse curso! Mas continue assim para manter sua conquista!
					</p>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="offer offer-warning">
				<div class="shape" style="">
					<div class="shape-text">
						Quests
					</div>
				</div>
				<div class="offer-content">
					<h3 class="lead">
						Você está quase lá...
					</h3>
					<p>
						Você deixou passar algumas atividades, mas não desanime, ainda tem um longo caminho pela frente!
					</p>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="offer offer-radius offer-danger">
				<div class="shape" style="">
					<div class="shape-text">
						Quests
					</div>
				</div>
				<div class="offer-content">
					<h3 class="lead">
						Tome cuidado...
					</h3>
					<p>
						Você ainda não respondeu nenhuma atividade deste curso, se deseja ter um resultado positivo no final, isto deve mudar.
					</p>
				</div>
			</div>
		</div>
	</div>
</div>';
  return $achievments_html;
}

function get_pet_life(){
  global $USER, $CFG;

  $courseid = required_param('id', PARAM_INT);
  $userid = $USER->id;

  $first_day = date('Y-m-d', key(get_course_registration_day($courseid, $userid)));

  $rem = time() - strtotime($first_day);
  $days = floor($rem / 86400) + 1;

  $pets_life = round(key(get_number_days_logged($first_day, $courseid, $userid))/$days * 100);

      if ($pets_life >= 75) {
          return
              '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
          			<div class="offer offer-success">
          				<div class="shape" style="">
          					<div class="shape-text">
          						Pet
          					</div>
          				</div>
          				<div class="offer-content">
          					<h3 class="lead">
          						Pai/Mãe de Pet
          					</h3>
          					<p>
          						Parabéns! Seu pet está com o máximo de vida
          					</p>
          				</div>
          			</div>
          		</div>';
      } elseif ($pets_life >= 50) {
        return
            '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        			<div class="offer offer-warning">
        				<div class="shape" style="">
        					<div class="shape-text">
        						Pet
        					</div>
        				</div>
        				<div class="offer-content">
        					<h3 class="lead">
        						Semi mais ou menos
        					</h3>
        					<p>
        						Não ta bom, mas também não ta ruim
                    <br> da pra melhorar né
        					</p>
        				</div>
        			</div>
        		</div>';
      } else {
        return
            '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        			<div class="offer offer-radius offer-danger">
        				<div class="shape" style="">
        					<div class="shape-text">
        						Pet
        					</div>
        				</div>
        				<div class="offer-content">
        					<h3 class="lead">
        						Cruella Cruel...
        					</h3>
        					<p>
        						Ok, talvez cuidar de animais
        						<br> não seja o melhor para você
        					</p>
        				</div>
        			</div>
        		</div>';
      }

}

?>

<!DOCTYPE html>
<html>
  <head>
    <style>
    .shape{
    	border-style: solid; border-width: 0 70px 40px 0; float:right; height: 0px; width: 0px;
    	-ms-transform:rotate(360deg); /* IE 9 */
    	-o-transform: rotate(360deg);  /* Opera 10.5 */
    	-webkit-transform:rotate(360deg); /* Safari and Chrome */
    	transform:rotate(360deg);
    }
    .offer{
    	background:#fff; border:1px solid #ddd; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); margin: 15px 0; overflow:hidden;
    }
    .shape {
    	border-color: rgba(255,255,255,0) #d9534f rgba(255,255,255,0) rgba(255,255,255,0);
    }
    .offer-radius{
    	border-radius:7px;
    }
    .offer-danger {	border-color: #d9534f; }
    .offer-danger .shape{
    	border-color: transparent #d9534f transparent transparent;
    }
    .offer-success {	border-color: #5cb85c; }
    .offer-success .shape{
    	border-color: transparent #5cb85c transparent transparent;
    }
    .offer-default {	border-color: #999999; }
    .offer-default .shape{
    	border-color: transparent #999999 transparent transparent;
    }
    .offer-primary {	border-color: #428bca; }
    .offer-primary .shape{
    	border-color: transparent #428bca transparent transparent;
    }
    .offer-info {	border-color: #5bc0de; }
    .offer-info .shape{
    	border-color: transparent #5bc0de transparent transparent;
    }
    .offer-warning {	border-color: #f0ad4e; }
    .offer-warning .shape{
    	border-color: transparent #f0ad4e transparent transparent;
    }

    .shape-text{
    	color:#fff; font-size:12px; font-weight:bold; position:relative; right:-40px; top:2px; white-space: nowrap;
    	-ms-transform:rotate(30deg); /* IE 9 */
    	-o-transform: rotate(360deg);  /* Opera 10.5 */
    	-webkit-transform:rotate(30deg); /* Safari and Chrome */
    	transform:rotate(30deg);
    }
    .offer-content{
    	padding:0 20px 10px;
    }
    @media (min-width: 487px) {
      .container {
        max-width: 750px;
      }
      .col-sm-6 {
        width: 50%;
      }
    }
    @media (min-width: 900px) {
      .container {
        max-width: 970px;
      }
      .col-md-4 {
        width: 33.33333333333333%;
      }
    }

    @media (min-width: 1200px) {
      .container {
        max-width: 1170px;
      }
      .col-lg-3 {
        width: 25%;
      }
      }
    }
    </style>
  </head>
</html>
