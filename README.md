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
	require_once("/vendor/autoload.php");

	$rop = new \jossmp\jne\rop(); // Registro de Organicaciones Politicas
	$essalud = new \jossmp\essalud\asegurado();
	$servir = new \jossmp\servir\servir();
	//$mtc = new \jossmp\mtc\conductor(); //Miniterio de trasporte y comunicaciones

	$dni = "44274795";

    $search1 = $rop->consulta( $dni );
	$search2 = $essalud->consulta( $dni );
	$search3 = $servir->consulta( $dni );

    if( $search1->success == true )
	{
		echo "Hola: " . $search1->result->nombres;
	}

	if( $search2->success == true )
	{
		echo "Hola: " . $search2->result->nombre;
	}

	if( $search2->success == true )
	{
		echo "Hola: " . $search3->result->nombre;
	}
?>
```
### Estructura datos EsSalud

```sh
{
    "success": true,
    "result": {
        "dni": "44274795",
        "verificacion": 0,
        "paterno": "MAZCO",
        "materno": "PUMA",
        "nombre": "JOSUE",
        "sexo": "Masculino",
        "nacimiento": "22/**/****",
        "gvotacion": null
    },
    "asegurado": null
}
```

### Estructura datos SERVIR y JNE(Registro de Org. Politicas)

```sh
{
    "success": true,
    "result": {
        "dni": "44274795",
        "verificacion": 0,
        "paterno": "MAZCO",
        "materno": "PUMA",
        "nombre": "JOSUE",
        "sexo": null,
        "nacimiento": null,
        "gvotacion": null
    }
}
```

### Estructura datos RENIEC - Padron electoral (No disponible)

```sh
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
```

### Mostrar Resultados en JSON / XML

```sh
<?php
	...
	if( $search->success == true )
	{
		echo $search->json( );
		echo $search->json( 'callback_js' );
	}

	if( $search->success == true )
	{
		echo PHP_EOL . $search->xml( );
		echo PHP_EOL . $search->xml( 'persona' ); // define nodo raiz
	}
?>
```

Donaciones: [PayPal]

Copyright (C), 2018 Josue Mazco GNU General Public License 3 (http://www.gnu.org/licenses/)

[paypal]: https://www.paypal.me/JossMP
