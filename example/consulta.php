<?php
	namespace DatosPeru;
	class Peru
	{
		function __construct()
		{
			$this->reniec = new \Reniec\Reniec(); 
			$this->essalud = new \EsSalud\EsSalud();
			$this->mintra = new \MinTra\mintra();
		}
		function search( $dni )
		{
			$response = $this->reniec->search( $dni );
			if($response->success == true)
			{
				$rpt = (object)array(
					"success" 		=> true,
					"source" 		=> "reniec",
					"result" 		=> $response->result
				);
				return $rpt;
			}
			
			$response = $this->essalud->check( $dni );
			if($response->success == true)
			{
				$rpt = (object)array(
					"success" 		=> true,
					"source" 		=> "essalud",
					"result" 		=> $response->result
				);
				return $rpt;
			}
						
			$response = $this->mintra->check( $dni );
			if( $response->success == true )
			{
				$rpt = (object)array(
					"success" 		=> true,
					"source" 		=> "mintra",
					"result" 		=> $response->result
				);
				return $rpt;
			}
			
			$rpt = (object)array(
				"success" 		=> false,
				"msg" 			=> "No se encontraron datos"
			);
			return $rpt;
		}
	}
	require_once("../src/autoload.php");
	
	$response = new \DatosPeru\Peru();
	
	header('Content-Type: text/plain');
	
	$dni = ( isset($_REQUEST["ndni"]))? $_REQUEST["ndni"] : false;
	echo json_encode( $response->search( $dni ) );
?>
