<?php
	namespace Reniec;
	class Reniec
	{
		var $cc;
		var $list_error = array();
		function __construct()
		{
			$this->cc = new \CURL\cURL();
			$this->cc->setReferer("http://clientes.reniec.gob.pe/padronElectoral2012/padronPEMDistrito.htm");
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
		
		/* SearchReniec
		 * 
		 * Realiza busqueda de datos enviado peticiones a la pagina de reniec
		 * 
		 * @param : string $dni 		CUI o numero de DNI
		 * 
		 * @return: array|false 		Array con datos encontrados en Reniec o false
		 * */
		function searchReniec( $dni )
		{
			$rtn=array();
			if( $dni != "" && strlen( $dni ) == 8 )
			{
				
				$data = array(
					"hTipo" 	=> "2",
					"hDni" 		=> $dni,
					"hApPat" 	=> "",
					"hApMat" 	=> "",
					"hNombre" 	=> ""
				);
				$url = "http://clientes.reniec.gob.pe/padronElectoral2012/consulta.htm";

				$response = $this->cc->send($url, $data);
				
				if($response)
				{
					libxml_use_internal_errors(true);

					$doc = new \DOMDocument();
					$doc->strictErrorChecking = FALSE;
					$doc->loadHTML( $response );
					libxml_use_internal_errors(false);

					$xml = simplexml_import_dom($doc);
					$result = $xml->xpath("//table");
					if( isset($result[4]) )
					{
						$result = $result[4];
						$rtn = array(
							"DNI" 			=> trim((string)$dni),
							"Nombres" 		=> trim(explode(",",(string)$result->tr[0]->td[1])[1]),
							"apellidos" 	=> trim(explode(",",(string)$result->tr[0]->td[1])[0]),
							"gvotacion" 	=> trim((string)$result->tr[2]->td[1]),
							"Distrito" 		=> trim((string)$result->tr[3]->td[1]),
							"Provincia" 	=> trim((string)$result->tr[4]->td[1]),
							"Departamento" 	=> trim((string)$result->tr[5]->td[1]),
						);
						return $rtn;
					}
				}
			}
			return false;
		}
		/* search
		 * 
		 * @param : string $dni 		CUI o numero de DNI
		 * @param : booleam $inJSON 	Cambia a true para retornar un string json
		 * 
		 * @return: object|string json 	Object o string JSON con datos encontrados
		 * */
		function search( $dni, $inJSON = false )
		{
			$dni = trim($dni);
			if( strlen( $dni )==8 && $dni!="" )
			{
				$result = $this->searchReniec($dni);
				if( $result!=false )
				{
					$rtn = (object)array(
						"success"	=> true,
						"result"	=> (object)$result
					);
					return ($inJSON==true)?json_encode($rtn,JSON_PRETTY_PRINT):$rtn;
				}
			}
			$rtn = (object)array(
				"success" 	=> false,
				"message" 	=> "Nro de DNI no valido.",
				"error" 	=> $this->list_error
			);
			return ($inJSON==true) ? json_encode($rtn,JSON_PRETTY_PRINT) : $rtn;
		}
	}
?>
