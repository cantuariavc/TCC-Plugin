<?php

function get_pet() {
    global $USER, $CFG;

    $courseid = required_param('id', PARAM_INT);
    $userid = $USER->id;
    
    $first_day = date('Y-m-d', key(get_course_registration_day($courseid, $userid)));

    $rem = time() - strtotime($first_day);
    $days = floor($rem / 86400) + 1;
    
    $pets_life = round(key(get_number_days_logged($first_day, $courseid, $userid))/$days * 100);

    
    
    return '
        <b>Vida do Bichinho Virtual</b></br>
        <div class="progress">
            <div class="progress-bar '.get_bar_color($pets_life).'" role="progressbar" aria-valuenow="'.$pets_life.'"
                    aria-valuemin="0" aria-valuemax="100" style="width:'.$pets_life.'%">
                <span>'.$pets_life.'</span>
            </div>
        </div>
        </br>
        <div style="vertical-align:middle; text-align:center">
            <img src="'.$CFG->wwwroot.'/blocks/game/pix/'.get_pet_status($pets_life).'.png"/>
        </div>';
}

function get_bar_color($pets_life) {
    if ($pets_life >= 75) {
        $bar_color = 'bg-success';
    } elseif ($pets_life >= 50) {
        $bar_color = 'bg-info';
    } elseif ($pets_life >= 25) {
        $bar_color = 'bg-warning';
    } else {
        $bar_color = 'bg-danger';
    }

    return $bar_color;
}

function get_pet_status($pets_life) {
    if ($pets_life >= 86) {
        $pet_image = 'cachorro-estado-1';
    } elseif ($pets_life >= 71) {
        $pet_image = 'cachorro-estado-2';
    } elseif ($pets_life >= 57) {
        $pet_image = 'cachorro-estado-3';
    } elseif ($pets_life >= 43) {
        $pet_image = 'cachorro-estado-4';
    } elseif ($pets_life >= 29) {
        $pet_image = 'cachorro-estado-5';
    } elseif ($pets_life >= 14) {
        $pet_image = 'cachorro-estado-6';
    } else {
        $pet_image = 'cachorro-estado-7';
    }

    return $pet_image;
}

?>