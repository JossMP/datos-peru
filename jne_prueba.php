<?php
header('Content-Type: text/txt');
require_once(__DIR__ . "/vendor/autoload.php");

$reniec = new \jossmp\jne\rop();

$dni = "44274795";

$response = $reniec->consulta($dni);

if ($response->success == true) {
	echo "[JNE] Hola: " . $response->result->nombre . ' ' . $response->result->paterno . ' ' . $response->result->materno . PHP_EOL;
	echo 'JSON OBJECT:' . PHP_EOL . $response->json(NULL, TRUE) . PHP_EOL;
}
