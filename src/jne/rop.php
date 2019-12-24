<?php

namespace jossmp\jne;

class rop
{
	var $curl = NULL;
	function __construct($config = array())
	{
		$this->curl = (new \jossmp\navigate\RequestCurl())->getCurl();

		if (isset($config["proxy"])) {
			$use 	= (isset($config["proxy"]["use"])) ? $config["proxy"]["use"] : FALSE;
			$host 	= (isset($config["proxy"]["host"])) ? $config["proxy"]["host"] : NULL;
			$port 	= (isset($config["proxy"]["port"])) ? $config["proxy"]["port"] : NULL;
			$type 	= (isset($config["proxy"]["type"])) ? $config["proxy"]["type"] : NULL;
			$user 	= (isset($config["proxy"]["user"])) ? $config["proxy"]["user"] : NULL;
			$pass 	= (isset($config["proxy"]["pass"])) ? $config["proxy"]["user"] : NULL;
			if ($use != FALSE) {
				$this->curl->setProxy($host, $port, $user, $pass);
				$this->curl->setProxyType($type);
			}
		}
		if (isset($config["cookie"])) {
			$use 	= (isset($config["cookie"]["use"])) ? $config["cookie"]["use"] : TRUE;
			$file 	= (isset($config["cookie"]["file"])) ? $config["cookie"]["file"] : 'cookie.txt';

			if ($use != FALSE) {
				$this->curl->setCookieFile($file);
				$this->curl->setCookieJar($file);
			}
		}
	}
	private function digit_control($dni)
	{
		if (strlen($dni) == 8 && is_numeric($dni)) {
			$suma = 0;
			$hash = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
			$suma = 5; // 10[NRO_DNI]X (1*5)+(0*4)
			for ($i = 2; $i < 10; $i++) {
				$suma += ($dni[$i - 2] * $hash[$i]); //3,2,7,6,5,4,3,2
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
		return NULL;
	}

	public function get_cookie()
	{
		$token = '';
		$url = 'http://aplicaciones007.jne.gob.pe/srop_publico/Consulta/Afiliado/';
		$response = $this->curl->get($url);
		if ($this->curl->getHttpStatusCode() == 200 && $response != '') {
			$patron = "/pTokenCookie\('(.*)'\)/";
			preg_match_all($patron, $response, $matches, PREG_SET_ORDER);
			if (isset($matches[0])) {
				$token_cookie = trim($matches[0][1]);
			}
			$patron = "/pTokenForm\('(.*)'\)/";
			preg_match_all($patron, $response, $matches, PREG_SET_ORDER);
			if (isset($matches[0])) {
				$token_form = trim($matches[0][1]);
			}
			if (isset($token_cookie) && isset($token_form)) {
				return $token_cookie . ':' . $token_form;
			}
		}
		return false;
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
		$token = $this->get_cookie();
		if ($token !== FALSE) {
			$this->curl->setHeader('RequestVerificationToken', $token);
			$post = array(
				"CODDNI" => $dni
			);

			$url = 'http://aplicaciones007.jne.gob.pe/srop_publico/Consulta/api/AfiliadoApi/GetNombresCiudadano';
			$response = $this->curl->post($url, $post);
			if ($this->curl->getHttpStatusCode() == 200 && $response != '') {

				$obj = (is_object($response)) ? $response : json_decode($response);
				if (is_object($obj) && isset($obj->data) && $obj->data != '||') {
					$part = explode('|', $obj->data);
					if (!empty($part) && count($part) == 3) {
						return new \jossmp\response\obj(array(
							'success' => true,
							'result' => array(
								'dni'            => $dni,
								'digito_control' => $this->digit_control($dni),
								'nombre'         => $part[2],
								'paterno'        => $part[0],
								'materno'        => $part[1]
							)
						));
					}
				}
				$return = new \jossmp\response\obj(array(
					'success' 	=> false,
					'notification' 	=> 'DNI Ingresado no encontrado.'
				));
				return $return;
			}
		}

		$return = new \jossmp\response\obj(array(
			'success' 	=> false,
			'notification' 	=> 'Fallo de coneccion.'
		));
		return $return;
	}
}
