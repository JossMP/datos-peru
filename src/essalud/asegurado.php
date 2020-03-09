<?php

namespace jossmp\essalud;

class asegurado
{
	var $curl = NULL;
	function __construct($config = array())
	{
		$this->curl = new \jossmp\navigate\Curl();

		$this->curl->setUserAgent('Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0');
		$this->curl->setReferer('https://ww1.essalud.gob.pe/sisep/postulante/postulante_registro.htm');
	}

	public function getCode($dni)
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

	public function consulta_asegurado($dni, $fecha_nacimiento)
	{
		// deleted

		$return = new \jossmp\response\obj(array(
			'success' 	=> false,
			'message' 	=> "Fallo de coneccion."
		));
		return $return;
	}

	function consulta_persona($dni)
	{
		if (strlen($dni) != 8 || !is_numeric($dni)) {
			$return = new \jossmp\response\obj(array(
				'success' => false,
				'message' => 'Debe Ingresar 8 digitos numericos.'
			));
			return $return;
		}
		$post = array(
			"strDni" 		=> $dni
		);
		$url = "https://ww1.essalud.gob.pe/sisep/postulante/postulante/postulante_obtenerDatosPostulante.htm";
		$response = $this->curl->post($url, $post);
		if ($this->curl->getHttpStatusCode() == 200 && ($response != '' || is_object($response))) {
			$obj = (is_object($response)) ? $response : json_decode($response);
			if (!empty($obj->DatosPerson[0]) && strlen($obj->DatosPerson[0]->DNI) >= 8 && $obj->DatosPerson[0]->Nombres != "") {
				$sexo = ((string) $obj->DatosPerson[0]->Sexo == '2') ? "Masculino" : "Femenino";
				$return = new \jossmp\response\obj(array(
					'success' => true,
					'result' => array(
						'dni'          => $dni,
						'verificacion' => $this->getCode($dni),
						'paterno'      => $obj->DatosPerson[0]->ApellidoPaterno,
						'materno'      => $obj->DatosPerson[0]->ApellidoMaterno,
						'nombre'       => $obj->DatosPerson[0]->Nombres,
						'sexo'         => $sexo,
						'nacimiento'   => $obj->DatosPerson[0]->FechaNacimiento,
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
		}
		$return = new \jossmp\response\obj(array(
			'success' => false,
			'message' => 'Coneccion fallida.'
		));
		return $return;
	}
	function consulta($dni)
	{
		$persona = $this->consulta_persona($dni);
		if ($persona->success == true) {
			$asegurado = $this->consulta_asegurado($persona->result->dni, $persona->result->nacimiento);
			if ($asegurado->success == true) {
				return $asegurado;
			} else {
				$persona->asegurado = NULL;
				return $persona;
			}
		}
		return $persona;
	}
}
