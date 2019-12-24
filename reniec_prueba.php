<?php
header('Content-Type: text/txt');
require_once(__DIR__ . "/vendor/autoload.php");

$reniec = new \jossmp\reniec\padron();

$dni = "44274795";

$response = $reniec->consulta($dni);

if ($response->success == true) {
	echo "[RENIEC] Hola: " . $response->result->nombres . ' ' . $response->result->apellidos . PHP_EOL;
	echo "Tu vives en: " . $response->result->departamento . ' / ' . $response->result->provincia . ' / ' . $response->result->distrito . PHP_EOL;

	echo 'JSON OBJECT:' . PHP_EOL . $response->json(NULL, TRUE) . PHP_EOL;
}
