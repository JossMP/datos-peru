<?php
	header('Content-Type: text/plain');

	require ("../src/autoload.php");

	$dni 		= ( isset($_REQUEST["ndni"]))? $_REQUEST["ndni"] : false;
	$source 	= ( isset($_REQUEST["source"]))? $_REQUEST["source"] : false;
	
	if( $source == "EsSalud" )
		$person = new \EsSalud\EsSalud();
	else
		$person = new \MinTra\mintra();
		
	$search 	= $person->search( $dni );
	
	echo $search->json();
?>
