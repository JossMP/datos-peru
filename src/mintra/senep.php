<?php

namespace jossmp\mintra;

class senep
{
	function __construct()
	{
		$this->curl = (new \jossmp\navigate\RequestCurl())->getCurl();
		$this->curl->setReferer('http://senep.trabajo.gob.pe:8080/');
	}
}
