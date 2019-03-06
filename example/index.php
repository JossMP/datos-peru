<?php
	session_start();
	$hora = date('H:i');
	$session_id = session_id();
	$token = hash('sha256', $hora.$session_id);
	 
	$_SESSION['token'] = $token;
?>
<!DOCTYPE html>
<html lang="es">
  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name = "viewport" content = "initial-scale = 1.0, user-scalable = no,  width=device-width">
		<title>JossMP/datos-peru</title>
		<meta name="description" content="Consulta datos de ciudadanos peruanos mediante su DNI"/>
		<meta property="og:locale" content="es_PE" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="Consulta datos de ciudadanos peruanos mediante su DNI" />
		<meta property="og:description" content="Consulta datos de ciudadanos peruanos con PHP sin captcha" />
		<meta property="og:image" content="https://drive.google.com/uc?id=0BxTe_c1GIOkoaFpkZlNrR0tta0E&export=view" />
		<!-- Bootstrap -->
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

		<style type="text/css">
			h1{
				margin:0px;
				padding:0px;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<h1 class="text-center p-3"> Consulta Datos RENIEC </h1>
			<form class="form-horizontal" role="form" method="post" name="busqueda" id="busqueda" >
				<div class="card border-info">
					<div class="card-header bg-info text-center text-light">
						Ingrese los datos requeridos
					</div>
					<div class="card-body">
						<input type="hidden" name="token" id="token" value="<?=$_SESSION['token'];?>">
						<input type="number" class="form-control" name="ndni" id="ndni" placeholder="Ingrese DNI" pattern="([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]|[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])" autofocus>
					</div>
					<div class="card-footer text-center">
						<button type="submit" class="btn btn-success" name="btn-submit-1" data-source="EsSalud" id="btn-submit-1">
							<i class="fa fa-search"></i> EsSalud
						</button>
						<button type="submit" class="btn btn-info" name="btn-submit-2" data-source="MinTra" id="btn-submit-2">
							<i class="fa fa-search"></i> MINTRA
						</button>
						<button type="submit" class="btn btn-warning" name="btn-submit-3" data-source="servir" id="btn-submit-3">
							<i class="fa fa-search"></i> SERVIR
						</button>
					</div>
				</div>
			</form>

			<div class="mx-auto text-center pt-2">
				<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- ads_Content -->
				<ins class="adsbygoogle"
					 style="display:inline-block;width:728px;height:90px"
					 data-ad-client="ca-pub-9492853452655822"
					 data-ad-slot="0854297903"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
				</script>
			</div>

			<div style="display:none" class="result">
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" href="#home" data-toggle="tab" role="tab">Respuesta</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#json" data-toggle="tab" role="tab">Json</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="home" role="tabpanel">
						<div class="card border-info" id="home">
							<div class="card-body">
								<table class="table table-striped">
									<tbody id="tbody">
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="json" role="tabpanel">
						<div class="card border-success">
							<div class="card-body">
								<pre id="json_code">
									Json code
								</pre>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div style="display:none" class="alert alert-danger" role="alert" id="error">
			</div>
			<footer class="footer text-center">
				<div class="col">
					<p><small>&copy; 2015 - 2018 JossMP - Derechos Reservados</small></p>
				</div>
			</footer>
		</div>
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
		<script src="js/ajaxview.js"></script>
		<script>
			$(document).ready(function(){
				$("button").click(function(e)
				{
					var $this = $(this);
					$source = $this.data("source");
					e.preventDefault();
					$.ajaxblock();
					$.ajax({
						data: {
							"ndni" : $("#ndni").val(),
							"source" : $source,
							"token" : $("#token").val()
						},
						type: "POST",
						dataType: "json",
						url: "consulta.php",
					}).done(function( data, textStatus, jqXHR ){
						if( data['success']!=false )
						{
							$("#json_code").text(JSON.stringify(data, null, '\t'));
							if(typeof(data['result'])!='undefined')
							{
								$("#tbody").html("");
								$.each(data['result'], function(i, v)
								{
									$("#tbody").append('<tr><td>'+i+'<\/td><td>'+v+'<\/td><\/tr>');
								});
							}
							//$this.button('reset');
							$("#error").hide();
							$(".result").show();
							$.ajaxunblock();
						}
						else
						{
							if(typeof(data['message'])!='undefined')
							{
								alert( data['message'] );
							}
							$.ajaxunblock();
						}
					}).fail(function( jqXHR, textStatus, errorThrown ){
						alert( "Solicitud fallida:" + textStatus );
						$.ajaxunblock();
					});
				});
			});
		</script>
	</body>
</html>

