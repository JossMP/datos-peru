<?php

namespace jossmp\servir;

class servir
{
	var $curl;
	var $list_error = array();
	function __construct()
	{
		$this->curl = (new \jossmp\navigate\RequestCurl())->getCurl();
		$this->curl->setReferer('https://www.sanciones.gob.pe/Sanciones/solicitudacceso');
	}

	/* getCode
	*
	* retorna codigo de verificacion de un DNI o CUI
	* @param : string $dni 		CUI o numero de DNI
	* @return: string 				Codigo de verificacion
	* */
	function getCode($dni)
	{
		if ($dni != "" || strlen($dni) == 8) {
			$suma = 0;
			$hash = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
			$suma = 5;
			for ($i = 2; $i < 10; $i++) {
				$suma += ($dni[$i - 2] * $hash[$i]);
			}
			$entero = (int) ($suma / 11);

			$digito = 11 - ($suma - $entero * 11);

			if ($digito == 10) {
				$digito = 0;
			} else if ($digito == 11) {
				$digito = 1;
			}
			return $digito;
		}
		return "";
	}

	/* consulta
	* Realiza busqueda de datos enviado peticiones a la pagina de reniec
	* @param : string $dni 		CUI o numero de DNI
	* @return: object
	* */
	function consulta($dni)
	{
		if (strlen($dni) != 8) {
			$response = new \jossmp\response\obj(array(
				'success' => false,
				'message' => 'Numero dni no valido.'
			));
			return $response;
		}

		$header = array(
			"X-Requested-With" 	=> "XMLHttpRequest"
		);
		$this->curl->setHeaders($header);

		$url = "https://www.sanciones.gob.pe/Sanciones/solicitudacceso.nrodocumento:bnrodocumentochanged?param=" . $dni;
		$data = array(
			"t:zoneid" 			=> "solicitudZone",
			"t:formid" 			=> "formSolicitud",
			"t:formcomponentid" => "SolicitudAcceso:formsolicitud"
		);
		$response = $this->curl->get($url);
		if ($this->curl->getHttpStatusCode() == 200 && $response != "") {
			$robj = (is_object($response)) ? $response : json_decode($response);
			if (is_object($robj)) {
				$url = "https://www.sanciones.gob.pe/Sanciones/solicitudacceso:buscartraba";
				$data = array(
					"t:zoneid" 			=> "solicitudZone",
					"t:formid" 			=> "formSolicitud",
					"t:formcomponentid" => "SolicitudAcceso:formsolicitud"
				);
				$response = $this->curl->post($url, $data);
				if ($this->curl->getHttpStatusCode() == 200 && $response != "") {
					$robj = (is_object($response)) ? $response : json_decode($response);
					if (is_object($robj) && isset($robj->content)) {
						libxml_use_internal_errors(true);

						$doc = new \DOMDocument();
						$doc->strictErrorChecking = FALSE;
						$doc->loadHTML($robj->content);
						libxml_use_internal_errors(false);

						$xml = simplexml_import_dom($doc);
						$result = $xml->xpath("//input");

						$return = new \jossmp\response\obj(array(
							'success' => true,
							'result' => array(
								'dni'          => $dni,
								'verificacion' => $this->getCode($dni),
								'paterno'      => (string) $result[2]->attributes()->value,
								'materno'      => (string) $result[3]->attributes()->value,
								'nombre'       => (string) $result[4]->attributes()->value,
								'sexo'         => NULL,
								'nacimiento'   => NULL,
								'gvotacion'    => NULL,
							)
						));
						return $return;
					} else {
						$return = new \jossmp\response\obj(array(
							'success' => false,
							'message' => 'Datos no encontrados.'
						));
						return $return;
					}
				} else {
					$return = new \jossmp\response\obj(array(
						'success' => false,
						'message' => 'Coneccion fallida.'
					));
					return $return;
				}
			} else {
				$return = new \jossmp\response\obj(array(
					'success' => false,
					'message' => 'No se puede procesar los datos.'
				));
				return $return;
			}
		} else {
			$return = new \jossmp\response\obj(array(
				'success' => false,
				'message' => 'Coneccion fallida.'
			));
			return $return;
		}
	}
}
