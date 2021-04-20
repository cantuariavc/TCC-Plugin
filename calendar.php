<?php


function get_calendar() {
    return mostrar_calendario_completo();
}

function mostrar_calendario_completo() {
    $calendario_table = '<table align="center"><th>'.date('Y').'</th>';
    $cont = 1;
    for ($j = 0; $j < 3; $j++) {
        $calendario_table .= '<tr>';
        for ($i = 0; $i < 4; $i++) {
            $calendario_table .= '<td>'.mostrar_calendario(($cont < 10 ) ? '0'.$cont : $cont).'</td>';
            $cont++;
        }
        $calendario_table .= '</tr>';
    }
    $calendario_table .= '</table>';

    return $calendario_table;
}

function mostrar_calendario($mes) {
    $numero_dias = get_numero_dias($mes);	// retorna o número de dias que tem o mês desejado
    
    $dias_logados = get_loged_dates($mes, $numero_dias);

	$nome_mes = get_nome_mes($mes);
	$diacorrente = 0;	
	$diasemana = jddayofweek(cal_to_jd(CAL_GREGORIAN, $mes, "01", date('Y')), 0);	// função que descobre o dia da semana
	$calendario = '
        <table border=0 cellspacing="0" align="center">
        <tr>
            <td colspan=7><h3>'.$nome_mes.'</h3></td>
        </tr>
        <tr>'.mostrar_semanas().'</tr>';	// função que mostra as semanas aqui
	for ($linha = 0; $linha < 6; $linha++) {
	    $calendario .= '<tr>';
 
	    for ($coluna = 0; $coluna < 7; $coluna++) {
            $calendario .= '<td width = 30 height = 30';
 
		    if (($diacorrente == (date('d') - 1) && date('m') == $mes)) {	
			    $calendario .= 'id="dia_atual"';
            } else {
                if (($diacorrente + 1) <= $numero_dias) {
			        if($coluna < $diasemana && $linha == 0) {
					    $calendario .= 'id="dia_branco"';
                    } else {
				  	    $calendario .= 'id="dia_comum"';
                    }
                } else {
				    $calendario .= ' ';
                }
            }
		    $calendario .= 'align="center" valign="center">';
 
 
		    /* TRECHO IMPORTANTE: A PARTIR DESTE TRECHO É MOSTRADO UM DIA DO CALENDÁRIO (MUITA ATENÇÃO NA HORA DA MANUTENÇÃO) */
 
		    if ($diacorrente + 1 <= $numero_dias) {
			    if ($coluna < $diasemana && $linha == 0) {
                    $calendario .= ' ';
			    } else {
			  	    // // echo "<input type = 'button' id = 'dia_comum' name = 'dia".($diacorrente+1)."'  value = '".++$diacorrente."' onclick = "acao(this.value)">";
                    
                    // Adicionar visualização de dia
                    // $calendario .= '<a href = '.$_SERVER['PHP_SELF'].'?mes=$mes&dia='.($diacorrente+1).'>'.++$diacorrente.'</a>';
                    
                    if (in_array($diacorrente + 1, $dias_logados)) {
                        $calendario .= '<a style="background-color:lightgreen">'.++$diacorrente.'</a>';
                    } else {
                        $calendario .= '<a>'.++$diacorrente.'</a>';
                    }
			    }
		    } else {
                break;
		    }
 
		    /* FIM DO TRECHO MUITO IMPORTANTE */
 
 
 
            $calendario .= "</td>";
	    }
        $calendario .= "</tr>";
	}
	$calendario .= "</table>";
    
    return $calendario;
}

function get_numero_dias($mes) {
    $ano = date('Y');

	$numero_dias = array( 
			'01' => 31, '02' => 28, '03' => 31, '04' =>30, '05' => 31, '06' => 30,
			'07' => 31, '08' =>31, '09' => 30, '10' => 31, '11' => 30, '12' => 31
	);
 
	if ((($ano % 4) == 0 and ($ano % 100)!=0) or ($ano % 400)==0)
	    $numero_dias['02'] = 29;	// altera o numero de dias de fevereiro se o ano for bissexto
 
	return $numero_dias[$mes];
}

function get_loged_dates($mes, $numero_dias) {
    global $USER;

    $couseid = required_param('id', PARAM_INT);
    $userid = $USER->id;

    $dias_logados = array();

    foreach (get_daily_login($couseid, $userid, false, $mes, $numero_dias) as $obj) {
        $date = explode(' ', $obj->loginday)[0];
        $day = explode('-', $date)[2];

        array_push($dias_logados, $day);
    }

    return $dias_logados;
}

function get_nome_mes($mes) {
    $meses = array( '01' => "Janeiro", '02' => "Fevereiro", '03' => "Março",
                    '04' => "Abril",   '05' => "Maio",      '06' => "Junho",
                    '07' => "Julho",   '08' => "Agosto",    '09' => "Setembro",
                    '10' => "Outubro", '11' => "Novembro",  '12' => "Dezembro"
                    );
 
    return $meses[$mes];
}

function mostrar_semanas() {
	$semanas = 'DSTQQSS';
    $dias_cols = '';
 
	for ($i = 0; $i < 7; $i++) {
        $dias_cols .= '<td>'.$semanas[$i].'</td>';
    }

    return $dias_cols;
}
?>
