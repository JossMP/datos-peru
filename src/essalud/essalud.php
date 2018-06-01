<?php
	namespace EsSalud;
	class EsSalud
	{
		function __construct()
		{
			$this->path = dirname(__FILE__);
			$this->cc = new \CURL\cURL();
			$this->cc->setReferer('https://ww1.essalud.gob.pe');
			$this->cc->setCookiFileLocation( __DIR__ . '/cookie.txt' );
		}
		function check( $dni )
		{
			$data = array(
				"strDni" 		=> $dni
			);
			$url = "https://ww1.essalud.gob.pe/sisep/postulante/postulante/postulante_obtenerDatosPostulante.htm";
			$response = $this->cc->send( $url, $data );
			if( $this->cc->getHttpStatus() == 200 && $response != "")
			{
				$json_Response = json_decode( $response );
				if( isset($json_Response->DatosPerson[0]) && count($json_Response->DatosPerson[0]) > 0 && strlen($json_Response->DatosPerson[0]->DNI)>=8 )
				{
					$rpt = (object)array(
						"success" 		=> true,
						"result" 		=> $json_Response->DatosPerson[0]
					);
					return $rpt;
				}
				else
				{
					$rpt = (object)array(
						"success" 		=> false,
						"message" 		=> "Datos no encontrados."
					);
					return $rpt;
				}
			}
			$rpt = (object)array(
				"success" 		=> false,
				"message" 		=> "failed connection."
			);
			return $rpt;
		}
	}
?>
