<?php
//PHP 5 +

// database settings 
$db_username = 'root';
$db_password = 'usbw';
$db_name = 'estoque';
$db_host = 'localhost';

//mysqli
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

if (mysqli_connect_errno()) 
{
	header('HTTP/1.1 500 Error: Could not connect to db!'); 
	exit();
}

################ Save & delete markers #################
if($_SERVER["REQUEST_METHOD"] == "POST") {

$titulo = $_POST["titulo"];
$endereco      = $_POST["txtEndereco"];
$uf       = $_POST["uf"];
$cidade       = $_POST["cidade"];
$tipo       = $_POST["tipo"];
$descricao       = $_POST["descricao"];
$latitude       = $_POST["txtLatitude"];
$longitude      = $_POST["txtLongitude"];

if(file_exists("init.php")) {
	require "init.php";		
} else {
	echo "Arquivo init.php nao foi encontrado";
	exit;
}

if(!function_exists("Abre_Conexao")) {
	echo "Erro o arquivo init.php foi auterado, nao existe a função Abre_Conexao";
	exit;
}

Abre_Conexao();
if(@mysql_query("INSERT INTO enderecos VALUES ( null,'$titulo', '$descricao', '$latitude', '$longitude', '$tipo' , '$uf', '$cidade', '$rua')")) {

	if(mysql_affected_rows() == 1){
		echo "Registro efetuado com sucesso<br />";
		header('Location: ../Enderecos.php'); 
	}	

} else {
	if(mysql_errno() == 1062) {
		echo $erros[mysql_errno()];
		exit;
	} else {	
		echo "Erro nao foi possivel efetuar o cadastro";
		exit;
	}	
	@mysql_close();
}

}

################ Continue generating Map XML #################

//Create a new DOMDocument object
$dom = new DOMDocument("1.0");
$node = $dom->createElement("sp"); //Create new element node
$parnode = $dom->appendChild($node); //make the node show up 

// Select all the rows in the markers table
$results = $mysqli->query("SELECT * FROM enderecos where 1");
if (!$results) {  
	header('HTTP/1.1 500 Error: Could not get markers!'); 
	exit();
} 

//set document header to text/xml
header("Content-type: text/xml"); 

// Iterate through the rows, adding XML nodes for each
while($obj = $results->fetch_object())
{
  $node = $dom->createElement("sp");  
  $newnode = $parnode->appendChild($node);   
  $newnode->setAttribute("name",$obj->name);
  $newnode->setAttribute("data",$obj->data);
  $newnode->setAttribute("hora",$obj->hora);
  $newnode->setAttribute("bo",$obj->bo);
  $newnode->setAttribute("address", $obj->address);  
  $newnode->setAttribute("lat", $obj->lat);  
  $newnode->setAttribute("lng", $obj->lng);  
  $newnode->setAttribute("rua", $obj->rua);   
  $newnode->setAttribute("uf", $obj->uf);	
  $newnode->setAttribute("cidade", $obj->cidade);	
  $newnode->setAttribute("type", $obj->type);
  	
}

echo $dom->saveXML();
?>