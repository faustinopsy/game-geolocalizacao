<?php
function monta_select($campo, $start, $end) {
	$select = "<select name=\"$campo\" id=\"$campo\">\n";
	for($i = $start; $i <= $end; $i++) {			
		$select .= "\t<option value=\"". sprintf("%02d", $i) ."\">".sprintf("%02d", $i)."</option>\n";	
	} 							
		$select .= "</select>\n";
	return $select;	
}	

function monta_combo($campo) {
	if($campo == "") {
		return false;
	}
	$select = "<select name=\"campo\">\n";
	for($i = 0; $i < count($campo); $i++) {			
		$select .= "\t<option value=\"$i\">{$campo[$i]}</option>\n";	
	} 							
		$select .= "</select>\n";
	return $select;	
}

function Pega_Preferencia($campo) {
	$campo = explode(",", $campo);
	for($i = 0; $i < count($campo); $i++) {
		switch ($campo[$i]) {
			case "R":
				$preferencia[] = "Romantico";		
				break;
			case "S":
				$preferencia[] = "Suspense";		
				break;
			case "P":
				$preferencia[] = "Policial";	
				break;
			case "F":
				$preferencia[] = "Ficção";	
				break;				
		}
	}
	return isset($preferencia) ? $preferencia : false;
}

function Seleciona_Item($valor, $campo) {
	return preg_replace("#<option value=\"$valor\">#is", "<option value=\"$valor\" selected=\"selected\">", $campo);
}
?>