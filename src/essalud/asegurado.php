<?php

namespace jossmp\essalud;

class asegurado
{
	function __construct()
	{
		$this->curl = (new \jossmp\navigate\RequestCurl())->getCurl();
		$this->curl->setReferer('https://ww1.essalud.gob.pe');
	}
}
