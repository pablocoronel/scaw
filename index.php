<?php
	session_start();
	include_once 'php/conexionDB.php';

	//Evitar ingreso sin login
	if (!isset($_SESSION['loginId'])){
		header('location: php/ingreso.php');
		exit();
	}
	/****************************************/
	$sqlArchivosSubidos= "SELECT * FROM archivo
							WHERE fkUsuarioCreador = ".$_SESSION['loginId'];
	$resArchivosSubidos= mysqli_query($conexion,$sqlArchivosSubidos);
	//Cantidad de archivos subidos
	$cantidadArchivosSubidos= mysqli_num_rows($resArchivosSubidos);
	/****************************************/
	//Lista usuarios editores:
	$sqlEditor= "SELECT idUsuario, usuario
									FROM usuario
										WHERE estadoRegistro= 1
										AND idUsuario != ".$_SESSION['loginId'];
	$respuestaEditor= mysqli_query($conexion,$sqlEditor) or die("Error al traer usuarios");
	//Lista de usuarios lectores:
	$sqlLector= "SELECT idUsuario, usuario
									FROM usuario
										WHERE estadoRegistro= 1
										AND idUsuario != ".$_SESSION['loginId'];
	$respuestaLector= mysqli_query($conexion,$sqlLector) or die("Error al traer usuarios");
	//Ver espacio utilizado y disponible:
	//Peso utilizado:
	$sqlPesoUsado= "SELECT SUM(peso) as peso, fkUsuarioCreador FROM archivo_version as av INNER JOIN archivo as ar
				ON av.fkArchivo = ar.idArchivo
				WHERE ar.fkUsuarioCreador = ".$_SESSION['loginId'];
	$resPesoUsado= mysqli_query($conexion,$sqlPesoUsado);
	$filaPesoUsado= mysqli_fetch_assoc($resPesoUsado);
	$pesoUsado= $filaPesoUsado['peso'];
	//Espacio asignado:
	$sqlEspacioAsignado= "SELECT espacioAsignado FROM usuario
		WHERE idUsuario = ".$_SESSION['loginId'];
	$resEspacioAsignado= mysqli_query($conexion,$sqlEspacioAsignado);
	$filaEspacioAsignado= mysqli_fetch_assoc($resEspacioAsignado);
	$espacioAsignado= $filaEspacioAsignado['espacioAsignado'];
	//Espacio disponible:
	$espacioDisponible= $espacioAsignado - $pesoUsado;
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/layout.css">
		<link rel="stylesheet" href="css/index.css">
		
		<script src="js/jquery-2.1.3.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		
		<script>
			$(document).ready(function(){
				$('#tipoCompartido1').click(function(){
					$('#divListaLectores').attr('class','panel-body hidden');
				});

				$('#tipoCompartido2').click(function(){
					$('#divListaLectores').attr('class','panel-body visible');
				});
			});
		</script>

		<title>Index - TP SCAW</title>
	</head>
	<body>
		<header>
			<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
				<div class="container">
					<!-- Cabecera del Menu -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navegacionProyectoFinal">
							<span class="sr-only">Desplegar / Ocultar Menu</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a href="index.php" class="navbar-brand">TP SCAW</a>
					</div>

					<!-- Cuerpo del Menu -->
					<div class="collapse navbar-collapse" id="navegacionProyectoFinal">
						<!-- Botones -->
						<ul class="nav navbar-nav navbar-right">
							<li class="active">
								<a href="#">
									Espacio Utilizado:
									<span class="badge">
										<?php
											echo(round(($pesoUsado / 1024 / 1024),2).' MB<br>');
										?>
									</span>
								</a>
							</li>

							<li class="active">
								<a href="#">
									Espacio Disponible:
									<span class="badge">
										<?php
											echo(round(($espacioDisponible / 1024 / 1024),2).' MB<br>');
										?>
									</span>
								</a>
							</li>
							
							<?php
								if (isset($_SESSION['loginNivel'])  && ($_SESSION['loginNivel'] == 'administrador')) {
							?>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
										Panel de Administración <span class="caret"></span>
									</a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="php/administracion.php">Usuarios</a></li>
										<li><a href="php/administracionArchivos.php">Archivos</a></li>
									</ul>
								</li>
							<?php
								}
							?>	
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
									<?php echo($_SESSION['loginUsuario']); ?> <span class="caret"></span>
								</a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="php/logout.php">Cerrar Sesión</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		
		<!-- Jumbotron -->
		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<!-- Contenido -->
		<section class="main container">
			<div class="row">
				<!-- Subir archivos -->
				<section id="subirArchivo" class="col-md-8">
					<form action="php/archivoSubir.php" method="post" enctype='multipart/form-data'>
						<div class="panel panel-primary">
							<div class="panel-heading text-center">
								<h4>Subir archivo</h4>
							</div>
							
							<div class="panel-body">
								<div class="form-group">
									<label for="archivo">Elegir Archivo</label>
									<input type="file" name="archivo" id="archivo">
								</div>
							</div>
							<div class="panel-body">
								<div class="form-group">
									<label for="">Compartir como: </label>
									<label for="tipoCompartido[]">P&uacute;blico</label>
									<input type="radio" name="tipoCompartido[]" id="tipoCompartido1" value="publico" checked="checked">
									<label for="tipoCompartido[]">Privado</label>
									<input type="radio" name="tipoCompartido[]" id="tipoCompartido2" value="privado">
								</div>
							</div>
							
							<!-- Editores (publico y privado) -->
							<div class="panel-body">
								<div class="form-group">
									<label for="listaEditores[]">Elegir Usuarios Editores</label>
								</div>
									<?php					
										while($filaEditor=mysqli_fetch_assoc($respuestaEditor)){
									?>
										<input type="checkbox" name="listaEditores[]" id="listaEditores[]" value="<?php echo($filaEditor['idUsuario']); ?>">
										<?php echo($filaEditor['usuario']); ?>
											<br>
									<?php
										}
									?>
							</div>
							
							<!-- Lectores (privado) -->
							<div class="panel-body hidden" id="divListaLectores">
								<div class="form-group" id="divLectores">
									<label for="listaLectores[]">Elegir Usuarios Lectores</label>
								</div>
									<?php					
										while($filaLector=mysqli_fetch_assoc($respuestaLector)){
									?>
											<input type="checkbox" name="listaLectores[]" id="listaLectores[]" value="<?php echo($filaLector['idUsuario']); ?>">
											<?php echo($filaLector['usuario']); ?>
											<br>
									<?php
										}
									?>
							</div>
							
							<!-- Boton subir archivo -->
							<div class="panel-body">
								<div class="form-group center">
									<div class="col-md-1.5 col-md-offset-5">
										<button type="submit" class="btn btn-success" value="Subir Archivo" name="subir" id="subir">Subir Archivo</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</section>

				<!-- Lista de mis archivos subidos -->
				<aside id="listaArchivosSubidos" class="col-md-4">
					<div class="panel panel-primary">
						<div class="panel-heading text-center">
							<h4>Mis archivos subidos
								<span class="badge pull-right"><?php echo($cantidadArchivosSubidos); ?></span>
							</h4>
						</div>
						<?php
							while ($filaArchivosSubidos= mysqli_fetch_assoc($resArchivosSubidos)) {
								$codigoArchivo= $filaArchivosSubidos['codigoArchivo'];
						?>
							<div class="panel-body">
								<a href="php/archivoVer.php?cod=<?php echo($codigoArchivo); ?>">
									<?php echo($filaArchivosSubidos['nombreArchivo']); ?>
								</a>
							</div>
						<?php
							}
						?>
					</div>
				</aside>
			</div>
		</section>
		
		<!-- Footer -->
		<footer class="jumbotron" id="jumbotronFooter">
			<div class="container">
				<div class="row">
					<div class="col-xs-4 col-xs-offset-4 text-center">
						<p>Pablo Coronel</p>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-8 col-xs-offset-2 text-center">
						Seguridad y Calidad en Aplicaciones Web - Universidad Nacional de La Matanza - Año 2015
					</div>
				</div>
			</div>
		</footer>

	</body>
</html>