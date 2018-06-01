<?php
	namespace Reniec;
	class MyDB extends \SQLite3
	{
		function __construct()
		{
			$this->open(dirname(__FILE__).'/solver.db');
		}
	}
?>
