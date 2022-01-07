<?php
//PHP 5 +

// database settings 
$db_username = 'root';
$db_password = 'usbw';
$db_name = 'google';
$db_host = 'localhost';

//mysqli
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

if (mysqli_connect_errno()) 
{
	header('HTTP/1.1 500 Error: Could not connect to db!'); 
	exit();
}

################ Save & delete markers #################
if($_POST) //run only if there's a post data
{
	//make sure request is comming from Ajax
	$xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'; 
	if (!$xhr){ 
		header('HTTP/1.1 500 Error: Request must come from Ajax!'); 
		exit();	
	}
	
	// get marker position and split it for database
	$mLatLang	= explode(',',$_POST["latlang"]);
	$mLat 		= filter_var($mLatLang[0], FILTER_VALIDATE_FLOAT);
	$mLng 		= filter_var($mLatLang[1], FILTER_VALIDATE_FLOAT);
	
	//Delete Marker
	if(isset($_POST["del"]) && $_POST["del"]==true)
	{
		
$results = $mysqli->query("INSERT INTO prisao(nome,poder,defesa, habilidade, lat, lng, type) SELECT nome,poder,defesa, habilidade, lat, lng, type FROM crime WHERE (WHERE lat=$mLat AND lng=$mLng)");
	
		$results = $mysqli->query("DELETE FROM crimes WHERE lat=$mLat AND lng=$mLng");
		if (!$results) {  
		  header('HTTP/1.1 500 Error: Could not delete crimes!'); 
		  exit();
		} 
		exit("Done!");
	}
	
	$mNome 		= filter_var($_POST["nome"], FILTER_SANITIZE_STRING);
	$mPoder 		= filter_var($_POST["poder"], FILTER_SANITIZE_STRING);
	$mDefesa 		= filter_var($_POST["defesa"], FILTER_SANITIZE_STRING);
	$mHabilidade 	= filter_var($_POST["habilidade"], FILTER_SANITIZE_STRING);
	
	$mType		= filter_var($_POST["type"], FILTER_SANITIZE_STRING);
	
	$results = $mysqli->query("INSERT INTO crimes (nome,poder,defesa, habilidade, lat, lng, type) VALUES ('$mNome','$mPoder','$mDefesa','$mHabilidade',$mLat, $mLng, '$mType')");
	if (!$results) {  
		  header('HTTP/1.1 500 Error: Could not create crimes!'); 
		  exit();
	} 
	
	$output = '<h1 class="marker-heading">'.$mNome.'</h1><p>'.$mHabilidade.'</p>';
	exit($output);
}


################ Continue generating Map XML #################

//Create a new DOMDocument object
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers"); //Create new element node
$parnode = $dom->appendChild($node); //make the node show up 

// Select all the rows in the markers table
$results = $mysqli->query("SELECT * FROM crimes where 1");
if (!$results) {  
	header('HTTP/1.1 500 Error: Could not get markers!'); 
	exit();
} 

//set document header to text/xml
header("Content-type: text/xml"); 

// Iterate through the rows, adding XML nodes for each
while($obj = $results->fetch_object())
{
  $node = $dom->createElement("markers");  
  $newnode = $parnode->appendChild($node);   
  $newnode->setAttribute("nome",$obj->nome);
  $newnode->setAttribute("poder",$obj->poder);
  $newnode->setAttribute("defesa",$obj->defesa);
  $newnode->setAttribute("habilidade", $obj->habilidade);  
  $newnode->setAttribute("lat", $obj->lat);  
  $newnode->setAttribute("lng", $obj->lng);  
  $newnode->setAttribute("type", $obj->type);	
  	
}

echo $dom->saveXML();
?>