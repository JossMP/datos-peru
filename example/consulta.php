<?php
header('Content-Type: text/plain');
session_start();
require("../vendor/autoload.php");

$dni 		= (isset($_REQUEST["ndni"])) ? $_REQUEST["ndni"] : false;
$source 	= (isset($_REQUEST["source"])) ? $_REQUEST["source"] : false;
$token   	= (isset($_REQUEST["token"])) ? $_REQUEST["token"] : false;

if ($token != $_SESSION["token"]) {
	echo json_encode(array(
		"success" => false,
		"message" => "Token caducado"
	));
} else {
	if ($source == "reniec") {
		$person = new \jossmp\reniec\padron();
		$response 	= $person->consulta($dni);
		echo $response->json();
	} else if ($source == "jne") {
		$person = new \jossmp\jne\rop();
		$response 	= $person->consulta($dni);
		echo $response->json();
	} else {
		echo json_encode(array(
			"success" => false,
			"message" => "Fuente no seleccionada"
		));
	}
}
