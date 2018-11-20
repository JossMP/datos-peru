<?php
	namespace servir;
	class servir
	{
		var $cc;
		var $list_error = array();
		function __construct()
		{
			$this->cc = new \CURL\cURL();
			$this->cc->setReferer("http://www.sanciones.gob.pe/Sanciones/solicitudacceso");
		}

		/* getCode
		 * 
		 * retorna codigo de verificacion de un DNI o CUI
		 * 
		 * @param : string $dni 		CUI o numero de DNI
		 * 
		 * @return: string 				Codigo de verificacion
		 * */
		function getCode( $dni )
		{
			if ($dni!="" || strlen($dni) == 8)
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
		
		/* Search
		 * 
		 * Realiza busqueda de datos enviado peticiones a la pagina de reniec
		 * 
		 * @param : string $dni 		CUI o numero de DNI
		 * 
		 * @return: object
		 * */
		function search( $dni )
		{
			if( strlen($dni)!=8 )
			{
				$response = new \response\obj(array(
					'success' => false,
					'message' => 'Numero dni no valido.'
				));
				return $response;
			}
			
			$header = array(
				"X-Requested-With" 	=> "XMLHttpRequest"
			);
			$this->cc->setHttpHeader($header);
			
			$url = "http://www.sanciones.gob.pe/Sanciones/solicitudacceso.nrodocumento:bnrodocumentochanged?param=" . $dni;
			$data = array(
				"t:zoneid" 			=> "solicitudZone",
				"t:formid" 			=> "formSolicitud",
				"t:formcomponentid" => "SolicitudAcceso:formsolicitud"
			);
			$response = $this->cc->send( $url );
			if( $this->cc->getHttpStatus()==200 && $response!="" )
			{
				$robj = json_decode($response);
				if( is_object($robj) )
				{
					$url = "http://www.sanciones.gob.pe/Sanciones/solicitudacceso:buscartraba";
					$data = array(
						"t:zoneid" 			=> "solicitudZone",
						"t:formid" 			=> "formSolicitud",
						"t:formcomponentid" => "SolicitudAcceso:formsolicitud"
					);
					$response = $this->cc->send( $url, $data );
					if( $this->cc->getHttpStatus()==200 && $response!="" )
					{
						$robj = json_decode( $response );
						if( is_object($robj) && isset($robj->content) )
						{
							libxml_use_internal_errors(true);

							$doc = new \DOMDocument();
							$doc->strictErrorChecking = FALSE;
							$doc->loadHTML( $robj->content );
							libxml_use_internal_errors(false);

							$xml = simplexml_import_dom($doc);
							$result = $xml->xpath("//input");

							$response = new \response\obj();
							$response->success = true;
							$response->source = 'sanciones.gob.pe';
							$response->result->dni = $dni;
							$response->result->verificacion = $this->getCode($dni);
							$response->result->paterno = (string)$result[2]->attributes()->value;
							$response->result->materno = (string)$result[3]->attributes()->value;
							$response->result->nombre = (string)$result[4]->attributes()->value;
							$response->result->sexo = null;
							$response->result->nacimiento = null;
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
					else
					{
						$response = new \response\obj(array(
							'success' => false,
							'message' => 'Coneccion fallida.'
						));
						return $response;
					}
				}
				else
				{
					$response = new \response\obj(array(
						'success' => false,
						'message' => 'No se puede procesar los datos.'
					));
					return $response;
				}
			}
			else
			{
				$response = new \response\obj(array(
					'success' => false,
					'message' => 'Coneccion fallida.'
				));
				return $response;
			}
		}
	}
?>
