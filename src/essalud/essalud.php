<?php
	namespace EsSalud;
	class EsSalud
	{
		function __construct()
		{
			$this->cc = new \CURL\cURL();
			$this->cc->setReferer('https://ww1.essalud.gob.pe');
		}
		function getCode( $dni )
		{
			if ($dni!="" && strlen($dni) == 8 && is_numeric($dni) )
			{
				$suma = 0;
				$hash = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
				$suma = 5;
				for( $i=2; $i<10; $i++ )
				{
					$suma += ( $dni[$i-2] * $hash[$i] );
				}
				$entero = (int)($suma/11);

				$digito = 11 - ( $suma - $entero*11);

				if ($digito == 10)
				{
					$digito = 0;
				}
				else if ($digito == 11)
				{
					$digito = 1;
				}
				return $digito;
			}
			return "";
		}
		function search( $dni )
		{
			if( strlen($dni)!=8 )
			{
				$response = new \response\obj(array(
					'success' => false,
					'message' => 'DNI tiene 8 digitos.'
				));
				return $response;
			}
			
			$data = array(
				"strDni" 		=> $dni
			);
			$url = "https://ww1.essalud.gob.pe/sisep/postulante/postulante/postulante_obtenerDatosPostulante.htm";
			$code = $this->cc->send( $url, $data );
			if( $this->cc->getHttpStatus() == 200 && $code != "")
			{
				$json = json_decode( $code );
				if( !empty($json->DatosPerson[0]) && isset($json->DatosPerson[0]->DNI) && strlen($json->DatosPerson[0]->DNI)>=8 && $json->DatosPerson[0]->Nombres != "" )
				{
					$sexo = ( (string)$json->DatosPerson[0]->Sexo == '2' ) ? "Masculino" : "Femenino";
					$response = new \response\obj();
					$response->success = true;
					$response->source = 'essalud.gob.pe';
					$response->result->dni = $json->DatosPerson[0]->DNI;
					$response->result->verificacion = $this->getCode($json->DatosPerson[0]->DNI);
					$response->result->paterno = $json->DatosPerson[0]->ApellidoPaterno;
					$response->result->materno = $json->DatosPerson[0]->ApellidoMaterno;
					$response->result->nombre = $json->DatosPerson[0]->Nombres;
					$response->result->sexo = $sexo;
					$response->result->nacimiento = $json->DatosPerson[0]->FechaNacimiento;
					$response->result->gvotacion = null;
					return $response;
				}
				else
				{
					$response = new \response\obj(array(
						'success' => false,
						'message' => 'Datos no encontrados.'
					));
					return $response;
				}
			}
			$response = new \response\obj(array(
				'success' => false,
				'message' => 'Coneccion fallida.'
			));
			return $response;
		}
	}
?>
