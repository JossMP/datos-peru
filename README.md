# Datos Perú

Busca datos de ciudadanos Peruanos a partir de su CUI o Numero de DNI.

### Metodo de Uso
```sh
<?php
	require_once( __DIR__ . "/src/autoload.php" );
	
	$essalud = new \EsSalud\EsSalud();
	$mintra = new \MinTra\mintra();
	
	$dni = "44274795";
	
    $search1 = $essalud->search( $dni );
	$search2 = $mintra->search( $dni );
    
    if( $search1->success == true )
	{
		echo "Hola: " . $search1->result->nombre;
	}
	
	if( $search2->success == true )
	{
		echo "Hola: " . $search2->result->nombre;
	}
?>
```
### Datos que se obtienen
```sh
<?php
	...
	$search = $essalud->search( $dni );
	$search = $mintra->search( $dni );
	
	$search->result->dni;
	$search->result->verificacion;
	$search->result->nombre;
	$search->result->paterno;
	$search->result->materno;
	$search->result->sexo;
	$search->result->nacimiento;
	$search->result->gvotacion; // NULL en EsSalud
?>
```
### Mostrar Resultados en JSON / XML
```sh
<?php
	...
	if( $search->success == true )
	{
		echo $search->json( );
		echo $search->json( 'callback' ); // para llamadas desde js
	}
	
	if( $search->success == true )
	{
		echo PHP_EOL . $search->xml( ); 
		echo PHP_EOL . $search->xml( 'persona' ); // define nodo raiz
	}
?>
```

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

Demo en linea: [Ver demo]
Donaciones: [PayPal]


Copyright (C), 2018 Josue Mazco GNU General Public License 3 (http://www.gnu.org/licenses/)

[Ver demo]: <https://www.peruanosenlinea.com/busca-personas-por-el-dni/>
[PayPal]: <https://www.paypal.me/JossMP>
