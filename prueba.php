<?php
	require_once( __DIR__ . "/src/autoload.php" );
	
	$essalud = new \EsSalud\EsSalud();
	$mintra = new \MinTra\mintra();
	
	$dni = "44274795";
	
    $search1 = $essalud->search( $dni );
	$search2 = $mintra->search( $dni );
    
    if( $search1->success == true )
	{
		echo PHP_EOL . "Hola: " . $search1->result->nombre;
	}
	
	if( $search2->success == true )
	{
		echo PHP_EOL . "Hola: " . $search2->result->nombre;
	}
	
	// --------- JSON / XML
	
	if( $search1->success == true )
	{
		echo PHP_EOL . $search1->json();
		echo PHP_EOL . $search1->json( 'callback' );
	}
	
	if( $search2->success == true )
	{
		echo PHP_EOL . $search2->xml();
		echo PHP_EOL . $search2->xml('persona');
	}
?>
