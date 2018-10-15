<?php
	session_start();
	include_once 'conexionDB.php';

	//Evita volver a loguearse o registrarse si ya se logueo
	if (isset($_SESSION['loginId'])) {
		header('location: ../index.php');
		exit();
	}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">
		<link rel="stylesheet" href="../css/ingreso.css">
		
		<script>
			function pestaniaActiva() {
				var anterior=1;
				
				if (anterior == 1){
					document.getElementById('seccion1').className='tab-pane active';
					document.getElementById('seccion2').className='tab-pane';
					document.getElementById('pestania1').className='active';
					document.getElementById('pestania2').className='';
				
				}else{
					document.getElementById('seccion1').className='tab-pane';
					document.getElementById('seccion2').className='tab-pane active';
					document.getElementById('pestania1').className='';
					document.getElementById('pestania2').className='active';		
				}
			}
		</script>
		<script src="../js/login.js"></script>
		<script src="../js/registro.js"></script>

		<!-- Recaptcha de google -->
		<script src='https://www.google.com/recaptcha/api.js'></script>

		<title>Ingreso - TP SCAW</title>
	</head>
	<body onLoad="javascript:pestaniaActiva();">
		<!--<header></header>-->
		
		<!-- Jumbotron -->
		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<!-- Contenido -->
		<section class="main container">
			<!-- Mensaje de error -->
		<?php 
			if (isset($_SESSION['error'])) {
		?>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="alert alert-danger">
						<p class="form-control-static text-center text-danger">
							<?php
								echo($_SESSION['error']);
								unset($_SESSION['error']);
							?>
						</p>
					</div>
				</div>
			</div>
		<?php
		}elseif (isset($_SESSION['registroCorrecto'])) {
		?>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="alert alert-success">
						<p class="form-control-static text-center text-success">
							<?php
								echo($_SESSION['registroCorrecto']);
								unset($_SESSION['registroCorrecto']);
							?>
						</p>
					</div>
				</div>
			</div>
		<?php
		}
		?>

			<!-- Formulario -->
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div role="tabpanel">
						<ul class="nav nav-pills nav-justified nav-success" role="tablist">
							<li role="presentation" id="pestania1"><a id="as" href="#seccion1" aria-controls="seccion1" data-toggle="tab" role="tab">Ingresar</a></li>
							<li role="presentation" id="pestania2"><a id="aa" href="#seccion2" aria-controls="seccion2" data-toggle="tab" role="tab">Registrarse</a></li>
						</ul>
						<!-- class="active" -->
					</div>
		
					<div class="tab-content">
						<!-- LOGIN -->
							<div role="tab-panel" class="tab-pane panel panel-body" id="seccion1">
								
								<!-- Formulario -->
								<form class="form-horizontal" action="loginProcesar.php" method="post" onSubmit="javascript:return controlDeLogin()">
									<fieldset>
										<div class="form-group has-default" id="inputUsuarioLogin">
											<label for="" class="control-label col-md-2">Usuario</label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="usuarioLogin" id="usuarioLogin" >
											</div>
											<label for="" class="control-label col-md-3 col-md-offset-0">
												<span id="spanUsuarioLogin"></span>
											</label>
										</div>
										
										<div class="form-group has-default" id="inputClaveLogin">
											<label for="correo" class="control-label col-md-2">Contrase&ntilde;a</label>
											<div class="col-md-7">
												<input type="password" class="form-control" name="claveLogin" id="claveLogin">
											</div>
											<label for="" class="control-label col-md-3 col-md-offset-0">
												<span id="spanClaveLogin"></span>
											</label>
										</div>
										
										<!-- <div class="form-group">
											<div class="col-md-6 col-md-offset-3">
												
											</div>
										</div> -->
							
										<div class="form-group">
											<div class="col-md-1.5 col-md-offset-5">
												<button class="btn btn-primary">Ingresar</button>
											</div>
										</div>
									</fieldset>
								</form>
							</div>
						<!-- REGISTRO -->
						<div role="tab-panel" class="tab-pane" id="seccion2">
							<!-- Formulario -->
							<form class="form-horizontal" action="registroProcesar.php" method="post" onSubmit="javascript:return controlDeRegistro();">
								<fieldset>
									<div class="form-group has-default" id="inputUsuarioRegistro">
										<label for="" class="control-label col-md-2">Usuario</label>
										<div class="col-md-7">
											<input type="text" class="form-control" name="usuarioRegistro" id="usuarioRegistro" placeholder="Letras, numeros o guiones (2 - 20)">
										</div>
										<label for="" class="control-label col-md-3 col-md-offset-0">
											<span id="spanUsuarioRegistro"></span>
										</label>
									</div>
									
									<div class="form-group has-default" id="inputClaveRegistro">
										<label for="correo" class="control-label col-md-2">Contrase&ntilde;a</label>
										<div class="col-md-7">
											<input type="password" class="form-control" name="claveRegistro" id="claveRegistro" placeholder="Letras, numeros, punto o guiones (6 - 20)">
										</div>
										<label for="" class="control-label col-md-3 col-md-offset-0">
											<span id="spanClaveRegistro"></span>
										</label>
									</div>
									
									<!-- <div class="form-group">
										<div class="col-md-6 col-md-offset-3">
											

										</div>
									</div> -->
									
									<!-- Captcha -->
									<div class="form-group">
										<label for="" class="control-label col-md-2">Captcha</label>
										<div class="col-md-7">
											<div class="g-recaptcha" data-sitekey="6Lc9aAwTAAAAAFL3HzS-6c0uF1UK6HvVotbF2P39"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="col-md-1.5 col-md-offset-5">
											<button class="btn btn-primary">Registrarse</button>
										</div>
									</div>
								</fieldset>
							</form>
						</div> <!-- Fin registro -->
					</div> <!-- Fin Panel -->
				</div>
			</div>
		</section>

		<!-- Footer -->
		<footer class="jumbotron navbar-fixed-bottom" id="jumbotronFooter">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-4 col-sm-offset-4 text-center">
						<p>Pablo Coronel</p>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-8 col-sm-offset-2 text-center">
						Seguridad y Calidad en Aplicaciones Web - Universidad Nacional de La Matanza - Año 2015
					</div>
				</div>
			</div>
		</footer>

		<script src="../js/jquery-2.1.3.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>