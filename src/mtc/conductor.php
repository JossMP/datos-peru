<?php

namespace jossmp\mtc;

class conductor
{
	var $curl = NULL;
	function __construct($config = array())
	{
		$this->curl = new \jossmp\navigate\Curl();
	}

	public function autentifica()
	{
		if (isset($_SESSION["auth_mtc"]) && $_SESSION["auth_mtc_time"] >= time() - 60 * 2) {
			$_SESSION["auth_mtc_time"] = time();
			return $_SESSION["auth_mtc"];
		}
		$url = 'https://icjc.mtc.gob.pe/frmRegistro.aspx';
		$response = $this->curl->post($url);
		if ($this->curl->getHttpStatusCode() == 200 && $response != '') {
			libxml_use_internal_errors(true);
			$doc = new \DOMDocument();
			$doc->strictErrorChecking = FALSE;
			$doc->loadHTML($response);
			libxml_use_internal_errors(false);

			$xml = simplexml_import_dom($doc);
			$__VIEWSTATE = $xml->xpath("//input[@id='__VIEWSTATE']");
			$__VIEWSTATEGENERATOR = $xml->xpath("//input[@id='__VIEWSTATEGENERATOR']");
			$__EVENTVALIDATION = $xml->xpath("//input[@id='__EVENTVALIDATION']");

			if (isset($__VIEWSTATE[0]) && isset($__VIEWSTATEGENERATOR[0]) && isset($__EVENTVALIDATION[0])) {
				$return = new \jossmp\response\obj(array(
					'success' => true,
					'result' => array(
						'__VIEWSTATE' 			=> (string) $__VIEWSTATE[0]->attributes()->value,
						'__VIEWSTATEGENERATOR' 	=> (string) $__VIEWSTATEGENERATOR[0]->attributes()->value,
						'__EVENTVALIDATION' 	=> (string) $__EVENTVALIDATION[0]->attributes()->value,
					)
				));
				$_SESSION["auth_mtc_time"] = time();
				$_SESSION["auth_mtc"] = $return;
				return $return;
			}
		}
		$return = new \jossmp\response\obj(array(
			'success' => false,
			'message' => 'Fallo de coneccion.'
		));
		return $return;
	}

	public function consulta($dni)
	{
		if (strlen($dni) != 8 || !is_numeric($dni)) {
			$return = new \jossmp\response\obj(array(
				'success' => false,
				'message' => 'Error DNI: Debe Ingresar 8 digitos numericos.'
			));
			return $return;
		}
		$auth = $this->autentifica();
		if ($auth->success == true) {
			$post = array(
				'ctl00$ScriptManager1' 	=> 'ctl00$ContentPlaceHolder1$updPRINCIPAL|ctl00$ContentPlaceHolder1$btnBuscar',
				'ctl00$ContentPlaceHolder1$ddlTipoDocumento' => 2,
				'ctl00$ContentPlaceHolder1$txtNumDocumento' => $dni,
				'hdnidconstancia' => '',
				'__VIEWSTATE' => $auth->result->__VIEWSTATE,
				'__VIEWSTATEGENERATOR' => $auth->result->__VIEWSTATEGENERATOR,
				'__EVENTVALIDATION' => $auth->result->__EVENTVALIDATION,
				'__LASTFOCUS' => '',
				'__EVENTTARGET' => '',
				'__EVENTARGUMENT' => '',
				'__ASYNCPOST' => true,
				'ctl00$ContentPlaceHolder1$btnBuscar' => 'Buscar Datos'
			);

			$url = 'https://icjc.mtc.gob.pe/frmRegistro.aspx';

			$response = $this->curl->post($url, $post);
			if ($this->curl->getHttpStatusCode() == 200) {
				libxml_use_internal_errors(true);

				$doc = new \DOMDocument();
				$doc->strictErrorChecking = FALSE;
				$doc->loadHTML($response);
				libxml_use_internal_errors(false);

				$xml = simplexml_import_dom($doc);
				$nombre_completo 		= $xml->xpath("//span[@id='ContentPlaceHolder1_txtNombreCompleto']");
				$nro_licencia 			= $xml->xpath("//span[@id='ContentPlaceHolder1_txtNrolicencia']");
				$direccion 				= $xml->xpath("//span[@id='ContentPlaceHolder1_txtDireccion']");
				$fecha_revalidacion 	= $xml->xpath("//span[@id='ContentPlaceHolder1_txtFechaRevalidacion']");
				$categoria_licencia 	= $xml->xpath("//span[@id='ContentPlaceHolder1_txtCategoria']");
				$fecha_nacimiento 		= $xml->xpath("//span[@id='ContentPlaceHolder1_txtFechaNacimiento']");
				if (isset($nombre_completo[0]) && trim($nombre_completo[0]) != "") {
					$return = new \jossmp\response\obj(array(
						'success' => true,
						'result' => new \jossmp\response\obj(array(
							'dni' 					=> trim((string) $dni),
							'nombre_completo' 		=> trim((string) $nombre_completo[0]),
							'fecha_nacimiento' 		=> trim((string) $fecha_nacimiento[0]),
							'direccion' 			=> trim((string) $direccion[0]),
							'nro_licencia' 			=> trim((string) $nro_licencia[0]),
							'categoria_licencia' 	=> trim((string) $categoria_licencia[0]),
							'fecha_revalidacion' 	=> trim((string) $fecha_revalidacion[0])
						))
					));
					return $return;
				} else {
					$return = new \jossmp\response\obj(array(
						'success' 	=> false,
						'message' 	=> 'Ningun resultado encontrado.'
					));
					return $return;
				}
			}
		}
		$return = new \jossmp\response\obj(array(
			'success' 	=> false,
			'message' 	=> 'Fallo de coneccion.'
		));
		return $return;
	}
}
