<?php
	namespace MinTra;
	class mintra
	{
		function __construct()
		{
			$this->cc = new \CURL\cURL();
			$this->cc->setReferer('http://senep.trabajo.gob.pe:8080/');
		}
		function getDataMinTra( $dni )
		{
			if(strlen(trim($dni))==8)
			{
				$url = 'http://senep.trabajo.gob.pe:8080/empleoperu/Ajax.do?method=obtenerCiudadanotoXML&POST_NUMDOCUM='.$dni;
				$response = $this->cc->send( $url );
				if($this->cc->getHttpStatus()==200 && $response!="")
				{
					$xml = new \SimpleXMLElement($response);
					
					$persona = $xml->CIUDADANO;
					if( $dni == (string)$persona->DNI )
					{
						$rtn = array(
							"DNI" 			=>(string)$persona->DNI,
							"paterno" 		=>(string)$persona->APELLIDOPAT,
							"materno" 		=>(string)$persona->APELLIDOMAT,
							"nombre" 		=>(string)$persona->NOMBRES,
							"sexo" 			=>(string)$persona->SEXO,
							"nacimiento" 	=>(string)$persona->FECHANAC,
							"gvotacion" 	=>(string)$persona->POST_GVOTACION
						);
						return $rtn;
					}
				}
			}
			return false;
		}
		function check( $dni, $inJSON = false )
		{
			if( strlen($dni) == 8 )
			{
				$info = $this->getDataMinTra( $dni );
				if( $info!=false )
				{
					$rtn = (object)array(
						"success" 	=> true,
						"result" 	=> $info
					);
				}
				else
				{
					$rtn = (object)array(
						"success" 	=> false,
						"msg" 		=> "No se ha encontrado resultados."
					);
				}
				return ($inJSON==true) ? json_encode($rtn,JSON_PRETTY_PRINT):$rtn;
			}

			$rtn = (object)array(
				"success" 	=> false,
				"msg" 		=> "Nro de DNI no valido."
			);
			return ($inJSON==true) ? json_encode( $rtn, JSON_PRETTY_PRINT ) : $rtn;
		}
	}
?>
