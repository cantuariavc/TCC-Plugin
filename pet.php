<?php

function get_pet() {
    global $USER;

    $courseid = required_param('id', PARAM_INT);
    $userid = $USER->id;
    
    $first_day = date('Y-m-d', key(get_course_registration_day($courseid, $userid)));

    $rem = time() - strtotime($first_day);
    $days = floor($rem / 86400) + 1;
    
    $pets_life = round(key(get_number_days_logged($first_day, $courseid, $userid))/$days * 100);

    if ($pets_life >= 75) {
        $bar_color = 'bg-success';
    } elseif ($pets_life >= 50) {
        $bar_color = 'bg-info';
    } elseif ($pets_life >= 25) {
        $bar_color = 'bg-warning';
    } else {
        $bar_color = 'bg-danger';
    }
    
    return '
        <b>Vida do Bichinho Virtual</b></br>
        <div class="progress">
            <div class="progress-bar '.$bar_color.'" role="progressbar" aria-valuenow="'.$pets_life.'"
                    aria-valuemin="0" aria-valuemax="100" style="width:'.$pets_life.'%">
                <span>'.$pets_life.'</span>
            </div>
        </div>
    ';
}

?>