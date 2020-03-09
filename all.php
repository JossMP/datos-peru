<?php
header('Content-Type: text/txt');
require_once(__DIR__ . "/vendor/autoload.php");

$rop = new \jossmp\jne\rop();
$dni = "44274795";
$response = $rop->consulta($dni);
if ($response->success == true) {
	echo PHP_EOL . '[ROP]:' . PHP_EOL . $response->json(NULL, TRUE) . PHP_EOL;
}

$essalud = new \jossmp\essalud\asegurado();
$dni = "44274795";
$response = $essalud->consulta($dni);
if ($response->success == true) {
	echo PHP_EOL . '[EsSalud]:' . PHP_EOL . $response->json(NULL, TRUE) . PHP_EOL;
}

$mtc = new \jossmp\mtc\conductor();
$dni = "42718060";
$response = $mtc->consulta($dni);
if ($response->success == true) {
	echo PHP_EOL . '[MTC]:' . PHP_EOL . $response->json(NULL, TRUE) . PHP_EOL;
}

$servir = new \jossmp\servir\servir();
$dni = "44274795";
$response = $servir->consulta($dni);
if ($response->success == true) {
	echo PHP_EOL . '[servir]:' . PHP_EOL . $response->json(NULL, TRUE) . PHP_EOL;
}
