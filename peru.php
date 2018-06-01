<?php
	namespace DatosPeru;
	class Peru
	{
		function __construct()
		{
			$this->essalud = new \EsSalud\EsSalud();
			$this->mintra = new \MinTra\mintra();
			$this->reniec = new \Reniec\Reniec(); // Fuente no disponible
		}
		function search( $dni )
		{
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
			else
			{
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
	//require_once( __DIR__ . "/vendor/autoload.php" ); // para comsposer
	$test = new \DatosPeru\Peru();
	print_r( $test->search("44274795") );
?>
