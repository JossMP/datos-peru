# Datos Perú

Obten los Nombres y apellidos de una Persona a partir de su Nro de DNI o CUI de cuidados Peruanos. puedes ver una demo [aqui].

### Metodo de Uso
```sh
<?php
    require ("./src/autoload.php");

    $reniec = new \Reniec\Reniec(); // Datos de Reniec (Padron de Electores)
    $essalud = new \EsSalud\EsSalud(); // Datos EsSalud
	$mintra = new \MinTra\mintra(); // Datos Ministerio del Trabajo
	
	$dni = "00000000";
	
    $persona1 = $reniec->search( $dni );
    $persona2 = $essalud->check( $dni );
    $persona3 = $mintra->check( $dni );
    
    if( $persona1->success ) // si la busqueda es exitosa
	{
		print_r( $persona2->result );
	}
	
	if( $persona2->success )
	{
		print_r( $persona2->result );
	}
	
	if( $persona3->success )
	{
		print_r( $persona3->result );
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

[aqui]: <https://demos.geekdev.ml/>
[PayPal]: <https://www.paypal.me/JossMP>

