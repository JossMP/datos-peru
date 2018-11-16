<?php
	namespace CURL;
	class cURL
	{
		protected $_useragent = 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';
		protected $_url;
		protected $_followlocation;
		protected $_timeout;
		protected $_httpheaderData = array();
		protected $_httpheader = array('Expect:');
		protected $_maxRedirects;
		protected $_cookieFileLocation;
		protected $_post;
		protected $_postFields;
		protected $_referer ="https://www.google.com/";

		protected $_session;
		protected $_webpage;
		protected $_includeHeader;
		protected $_noBody;
		protected $_status;
		protected $_binary;
		protected $_binaryFields;

		public    $proxy = false;
		public    $proxy_host = '';
		public    $proxy_port = '';
		public    $proxy_type = CURLPROXY_HTTP;
		
		public    $authentication = false;
		public    $auth_name      = '';
		public    $auth_pass      = '';

		public function __construct( $followlocation = true, $timeOut = 30, $maxRedirecs = 4, $binary = false, $includeHeader = false, $noBody = false )
		{
			$this->_followlocation = $followlocation;
			$this->_timeout = $timeOut;
			$this->_maxRedirects = $maxRedirecs;
			$this->_noBody = $noBody;
			$this->_includeHeader = $includeHeader;
			$this->_binary = $binary;

			$this->_cookieFileLocation = __DIR__ . '/cookie.txt';
			$this->s = curl_init();
		}
		
		public function __destruct()
		{
			curl_close($this->s);
		}
		
		public function useProxy( $use )
		{
			$this->proxy = false;
			if($use == true) $this->proxy = true;
		}
		public function setHost( $host )
		{
			$this->proxy_host = $host;
		}
		public function setPort( $port )
		{
			$this->proxy_port = $port;
		}
		public function setTypeProxy( $type ) //
		{
			// CURLPROXY_SOCKS5 | CURLPROXY_SOCKS4 | CURLPROXY_HTTP
			// 5 | 4 | 0
			$this->proxy_type = $type;
		}

		public function useAuth( $use )
		{
			$this->authentication = false;
			if($use == true) $this->authentication = true;
		}

		public function setName( $name )
		{
			$this->auth_name = $name;
		}
		public function setPass( $pass )
		{
			$this->auth_pass = $pass;
		}

		public function setReferer( $referer )
		{
			$this->_referer = $referer;
		}

		public function setHttpHeader( $httpheader=array() )
		{
			$this->_httpheader = array();
			foreach( $httpheader as $i=>$v )
			{
				$this->_httpheaderData[$i]=$v;
			}
			foreach( $this->_httpheaderData as $i=>$v )
			{
				$this->_httpheader[]=$i.":".$v;
			}
		}

		public function setCookiFileLocation( $path )
		{
			$this->_cookieFileLocation = $path;
			if ( !file_exists($this->_cookieFileLocation) )
			{
				file_put_contents($this->_cookieFileLocation,"");
			}
		}

		public function setPost( $postFields = array() )
		{
			$this->_binary = false;
			$this->_post = false;
			if(count($postFields)>0)
			{
				$this->_post = true;
			}
			$this->_postFields = http_build_query($postFields);
		}

		public function setBinary( $postBinaryFields = "" )
		{
			$this->_post = false;
			$this->_binary = false;
			if(strlen($postBinaryFields)>0)
			{
				$this->_binary = true;
			}
			$this->_binaryFields = $postBinaryFields;
		}

		public function setUserAgent( $userAgent )
		{
			$this->_useragent = $userAgent;
		}

		public function createCurl( $url = 'nul' )
		{
			if($url != 'nul')
			{
				$this->_url = $url;
			}

			//$this->s = curl_init();
			//curl_setopt($this->s, CURLOPT_FAILONERROR, true);
			//curl_setopt($this->s, CURLOPT_HEADER, true);
			//curl_setopt($this->s, CURLOPT_VERBOSE, true);
			//curl_setopt($this->s, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($this->s, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->s, CURLOPT_URL,$this->_url);
			curl_setopt($this->s, CURLOPT_HTTPHEADER,$this->_httpheader);
			curl_setopt($this->s, CURLOPT_TIMEOUT,$this->_timeout);
			curl_setopt($this->s, CURLOPT_MAXREDIRS,$this->_maxRedirects);
			curl_setopt($this->s, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($this->s, CURLOPT_FOLLOWLOCATION,$this->_followlocation);
			curl_setopt($this->s, CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
			curl_setopt($this->s, CURLOPT_COOKIEFILE,$this->_cookieFileLocation);

			if($this->proxy == true)
			{
				if( $this->proxy_host != '' && $this->proxy_port != '' )
				{
					curl_setopt($this->s, CURLOPT_HTTPPROXYTUNNEL, 0);
					curl_setopt($this->s, CURLOPT_PROXY, $this->proxy_host.':'.$this->proxy_port);
					curl_setopt($this->s, CURLOPT_PROXYTYPE, $this->proxy_type);
				}
			}
			
			if($this->authentication == true)
			{
				curl_setopt($this->s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
			}

			if($this->_post)
			{
				curl_setopt($this->s, CURLOPT_POST, true);
				curl_setopt($this->s, CURLOPT_POSTFIELDS, $this->_postFields);
			}

			if($this->_binary)
			{
				curl_setopt($this->s, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($this->s, CURLOPT_POSTFIELDS, $this->_binaryFields);
			}

			if( $this->_includeHeader )
			{
				curl_setopt($this->s, CURLOPT_HEADER, true);
			}

			if( $this->_noBody )
			{
				curl_setopt($this->s, CURLOPT_NOBODY, true);
			}

			curl_setopt( $this->s, CURLOPT_USERAGENT, $this->_useragent );
			curl_setopt( $this->s, CURLOPT_REFERER, $this->_referer );
			$this->_webpage = curl_exec( $this->s );
			$this->_status = curl_getinfo( $this->s, CURLINFO_HTTP_CODE );
			//curl_close( $this->s );
		}

		public function getHttpStatus()
		{
			return $this->_status;
		}

		public function __toString()
		{
			return $this->_webpage;
		}
		// simplificado
		public function send( $url, $post = array() )
		{
			$this->_post = false;
			if( count($post)!=0 )
				$this->setPost( $post );

			$this->createCurl( $url );
			return $this->_webpage;
		}
		public function sendBinary( $url, $binary="" )
		{
			$this->_binary = false;
			if( $binary != "" )
			{
				$this->setBinary( $binary );
				$this->setHttpHeader( array('Content-Length'=>strlen($this->_binaryFields)) );
				$this->setHttpHeader( array('Content-Type'=>'application/json;charset=utf-8') );
				$this->setHttpHeader( array('Access-Control-Allow-Origin'=>'*') );
			}
			$this->createCurl( $url );
			return $this->_webpage;
		}
	}
?>
