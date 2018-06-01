<?php
	namespace Reniec;
	class Reniec
	{
		var $cc;
		var $db;
		var $list_error = array();
		function __construct()
		{
			$this->db = new \Reniec\MyDB();
			
			$this->cc = new \CURL\cURL();
			//date_default_timezone_set('America/Lima');
			/*
			if( !session_id() )
			{
				session_start();
				session_cache_limiter('private');
				session_cache_expire(2); // 2 min.
			}
			*/
		}
		
		/* getSession
		 * 
		 * Retorna el valor de una session
		 * 
		 * @param : string $indice 		Indice o identificador de $_SESSION
		 * 
		 * @return: mixed $return 		Valor que se almacenara en $_SESSION[$indice]
		 * */
		 function getSession($indice)
		{
			if(isset($_SESSION[$indice]))
			{
				return $_SESSION[$indice];
			}
			return false;
		}
		
		/* setSession
		 * 
		 * Registra o modifica el array $_SESSION
		 * 
		 * @param : string $indice		Indice o identificador de $_SESSION
		 * @param : mixed $valor		Valor que se almacenara en $_SESSION
		 * 
		 * @return: booleam true
		 * */
		function setSession($indice, $valor)
		{
			$_SESSION[$indice] = $valor;
			return true;
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
		
		/* downCaptcha
		 * 
		 * Descargar la imagen captcha
		 * 
		 * @param : string $name 		Nombre con el que se guardara el Archivo
		 * 
		 * @return: booleam ( true | false )
		 * */
		function downCaptcha($name)
		{
			$ref="https://cel.reniec.gob.pe/valreg/valreg.do";
			$url="https://cel.reniec.gob.pe/valreg/codigo.do";
			$this->cc->setReferer($ref);
			$captcha = $this->cc->send($url);
			if($captcha!=false)
			{
				$file = file_put_contents($name, $captcha);
				if( $file )
					return true;
				else
					$this->list_error[] = "No se Puede guardar el captcha.";
			}
			else
			{
				$this->list_error[] = "No se Puede Descargar el captcha.";
			}
			return false;
		}
		
		/* getCaptcha
		 * 
		 * Retorna una string coN el valor de la imagen captcha
		 * 
		 * @param : string $name 		Nombre con el que se guardara el Archivo
		 * 
		 * @return: mixed 				string con valor del Captcha o false
		 * */
		function getCaptcha( $name = "captcha.jpg" )
		{
			/*$captcha = $this->getSession("captcha");
			$stime = $this->getSession("stime");
			if( $captcha != false && $stime+(2*60) > time() )
			{
				return $captcha;
			}
			*/
			$name = __DIR__ . "/" . $name;
			if($this->downCaptcha( $name ))
			{
				$image = @imagecreatefromjpeg( $name );
				if( $image )
				{
					imagefilter( $image, IMG_FILTER_GRAYSCALE );
					imagefilter( $image, IMG_FILTER_BRIGHTNESS,100 );
					imagefilter( $image, IMG_FILTER_NEGATE );
					$L1 = imagecreatetruecolor(25, 20);
					$L2 = imagecreatetruecolor(25, 20);
					$L3 = imagecreatetruecolor(25, 20);
					$L4 = imagecreatetruecolor(25, 20);

					imagecopyresampled($L1, $image, 0, 0, 13, 10, 25, 20, 25, 20);
					imagecopyresampled($L2, $image, 0, 0, 43, 15, 25, 20, 25, 20);
					imagecopyresampled($L3, $image, 0, 0, 76, 10, 25, 20, 25, 20);
					imagecopyresampled($L4, $image, 0, 0, 106,15, 25, 20, 25, 20);

					$query = "SELECT (SELECT Caracter FROM Diccionario WHERE Codigo1='".$this->getText($L1)."') AS c1,(SELECT Caracter FROM Diccionario WHERE Codigo2='".$this->getText($L2)."') AS c2,(SELECT Caracter FROM Diccionario WHERE Codigo3='".$this->getText($L3)."') AS c3,(SELECT Caracter FROM Diccionario WHERE Codigo4='".$this->getText($L4)."') AS c4";

					$rpt = $this->db->query($query);
					if( $row = $rpt->fetchArray(SQLITE3_ASSOC) )
					{
						return $row["c1"].$row["c2"].$row["c3"].$row["c4"];
					}
					else
					{
						$this->list_error[] = "No se puede encontrar resultados (sqlite3)";
					}
				}
				else
				{
					$this->list_error[] = "No se puede procesar el captcha.";
				}
			}
			return false;
		}
		
		/* getText
		 * 
		 * Retorna una string de 0s y 1s
		 * 
		 * @param : image $image 		Segmento de imagen del captcha
		 * 
		 * @return: string 				cadena de 0s y 1s
		 * */
		function getText($image)
		{
			$rtn="";
			$w = imagesx($image);
			$h = imagesy($image);
			for($y=0; $y<$h;$y++)
			{
				for($x=0; $x<$w;$x++)
				{
					$rgb = imagecolorat($image, $x, $y);
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;
					if((($r+$g+$b)/255) < 1)
					{
						$rtn.="0";
					}
					else
					{
						$rtn.="1";
					}
				}
			}
			return $rtn;
		}
		
		/* SearchReniec
		 * 
		 * Realiza busqueda de datos enviado peticiones a la pagina de reniec
		 * 
		 * @param : string $dni 		CUI o numero de DNI
		 * 
		 * @return: array|false 		Array con datos encontrados en Reniec o false
		 * */
		function searchReniec($dni)
		{
			$rtn=array();
			$Captcha = $this->getCaptcha("captcha.jpg");
			if( $dni != "" && strlen( $dni ) == 8 && $Captcha != false )
			{
				$data = array(
					"accion" 	=> "buscar",
					"nuDni" 	=> $dni,
					"imagen" 	=> $Captcha
				);
				$url = "https://cel.reniec.gob.pe/valreg/valreg.do";
				$this->cc->setReferer($url);
				$Page = $this->cc->send($url, $data);

				$patron='/<td height="63" class="style2" align="center">\r\n[ ]+(.*)\r\n[ ]+(.*)\r\n[ ]+(.*)<br>/';
				$output = preg_match_all($patron, $Page, $matches, PREG_SET_ORDER);
				if( isset($matches[0]) )
				{
					$rtn["Nombre"]  = utf8_encode($matches[0][1]);
					$rtn["Paterno"] = utf8_encode($matches[0][2]);
					$rtn["Materno"] = utf8_encode($matches[0][3]);
				}
				else
				{
					$this->list_error[] = "No se puede procesar el patron de busqueda";
				}

				$patron='/<font color=#ff0000>([A-Z0-9]+) <\/font>/';
				$output = preg_match_all($patron, $Page, $matches, PREG_SET_ORDER);
				if( isset($matches[0]) )
				{
					$rtn["DNI"] = $dni;
					$rtn["CodVerificacion"] = trim($matches[0][1]);
				}
				if( count($rtn)>0 )
				{
					return $rtn;
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
						"result"	=> (object)$result,
						"error" 	=> $this->list_error
					);
					return ($inJSON==true)?json_encode($rtn,JSON_PRETTY_PRINT):$rtn;
				}
			}
			$rtn = (object)array(
				"success" 	=> false,
				"msg" 		=> "Nro de DNI no valido.",
				"error" 	=> $this->list_error
			);
			return ($inJSON==true) ? json_encode($rtn,JSON_PRETTY_PRINT) : $rtn;
		}
	}
?>
