# Datos Perú

Busca datos de ciudadanos Peruanos a partir de su CUI o Numero de DNI.

### Instalacion mediante composer

```sh
	composer require -o "jossmp/datos-peru"
```

```sh
<?php
    require ("./vendor/autoload.php");
    ...
?>
```

### Pre-requisitos

```sh
- cURL
- PHP 5.2.0 o superior
```

### Metodo de Uso

```sh
<?php
	require_once("../vendor/autoload.php" );

	$reniec = new \jossmp\reniec\padron(); // Padron electoral RENIEC
	$jne = new \jossmp\jne\rop(); // Registro de Org. Politicas JNE

	$dni = "44274795";

    $search1 = $reniec->consulta( $dni );
	$search2 = $jne->consulta( $dni );

    if( $search1->success == true )
	{
		echo "Hola: " . $search1->result->nombres;
	}

	if( $search2->success == true )
	{
		echo "Hola: " . $search2->result->nombre;
	}
?>
```

### Estructura datos RENIEC(Padron electoral)

```sh
<?php
	...
{
	"success": true,
	"result": {
		"dni": "44274795",
		"digito_control": 0,
		"nombres": "JOSUE",
		"apellidos": "MAZCO PUMA",
		"gvotacion": "244954",
		"distrito": "AZANGARO",
		"provincia": "AZANGARO",
		"departamento": "PUNO"
	}
}
?>
```

### Estructura datos JNE(Registro de Org. Politicas)

```sh
<?php
	...
{
	"success": true,
	"result": {
		"dni": "44274795",
		"digito_control": 0,
		"nombre": "JOSUE",
		"paterno": "MAZCO",
		"materno": "PUMA"
	}
}
?>
```

### Mostrar Resultados en JSON / XML

```sh
<?php
	...
	if( $search->success == true )
	{
		echo $search->json( );
		echo $search->json( 'callback' ); // para llamadas desde JS
	}

	if( $search->success == true )
	{
		echo PHP_EOL . $search->xml( );
		echo PHP_EOL . $search->xml( 'persona' ); // define nodo raiz
	}
?>
```

Demo en linea: [Ver demo]

Donaciones: [PayPal]

Copyright (C), 2018 Josue Mazco GNU General Public License 3 (http://www.gnu.org/licenses/)

[ver demo]: https://www.peruanosenlinea.com/busca-personas-por-el-dni/
[paypal]: https://www.paypal.me/JossMP
