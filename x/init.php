<?php
error_reporting(E_ALL);
@ini_set('display_errors', '1');
@ini_set('register_globals', '0');
if (version_compare(phpversion(), "4", ">")) { 
	if (!extension_loaded('mysql')) {
		echo( "Nao esta habilitada a dll Mysql" );
		exit;
	}					
} 

if(file_exists("funcoes.php")) {
	include "funcoes.php";	
} else {
	echo "Arquivo funcoes.php nao encontrado";
	exit;
}	

if(file_exists("config.php")) {			
	include "config.php";

	if (!defined("SERVIDOR") or !defined("USUARIO") or !defined("SENHA") or !defined("BANCO")){
		echo "Variaveis de conexao nao definidas, configure corretamente o arquivo config.php";
		exit;
	}
}

$erros[2005] = "Esse servidor nao existe";
$erros[2003] = "Servidor Mysql desligado";
$erros[1045] = "Usuario ou senha invalido";
$erros[1049] = "Banco de dados nao encontrado";
$erros[1146] = "Erro de sql a tabela nao existe";
$erros[1062] = "Erro campo unico na tabela, nao pode cadastrar pois ele ja existe";

function Abre_Conexao() {	
	global $erros;
	@mysql_connect(SERVIDOR, USUARIO, SENHA);
	if(mysql_errno() != 0) {
		echo $erros[mysql_errno()];	
		exit;	
	}	
	@mysql_select_db(BANCO);		
	if(mysql_errno() != 0) {
		echo $erros[mysql_errno()];	
		exit;
	}		
}
	
?>