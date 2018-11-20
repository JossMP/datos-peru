<?php
	header('Content-Type: text/plain');
	session_start();
	require ("../src/autoload.php");

	$dni 		= ( isset($_REQUEST["ndni"]))? $_REQUEST["ndni"] : false;
	$source 	= ( isset($_REQUEST["source"]))? $_REQUEST["source"] : false;
	$token 	= ( isset($_REQUEST["token"]))? $_REQUEST["token"] : false;
	
	if( $token != $_SESSION["token"] )
	{
		echo json_encode( array(
			"success" => false,
			"message" => "Token caducado"
		));
		exit();
	}
	
	if( $source == "EsSalud" )
		$person = new \EsSalud\EsSalud();
	else if( $source == "servir" )
		$person = new \servir\servir();
	else
		$person = new \MinTra\mintra();
		
	$search 	= $person->search( $dni );
	
	echo $search->json();
?>
