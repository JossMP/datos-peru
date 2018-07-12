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
	
	// MODO DE USO
	/*  */
	require_once( __DIR__ . "/src/autoload.php" );
	//require_once( __DIR__ . "/vendor/autoload.php" ); // si se usa composer
	$test = new \DatosPeru\Peru();
	print_r( $test->search("44274795") );
?>
