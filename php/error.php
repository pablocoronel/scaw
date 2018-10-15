<?php
	session_start();
	include_once 'conexionDB.php';

	//Evitar ingreso sin login
	if (!isset($_SESSION['error'])){
		header('location: ../index.php');
		exit();
	}
	/*************************************************/
	//Ver espacio utilizado y disponible:
	//Peso utilizado:
if (isset($_SESSION['loginId'])) {
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
}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">

		<title>Error - TP SCAW</title>
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
						<a href="../index.php" class="navbar-brand">TP SCAW</a>
					</div>

					<?php
						if (isset($_SESSION['loginId'])) {
					?>
					<!-- Cuerpo del Menu -->
					<div class="collapse navbar-collapse" id="navegacionProyectoFinal">
						<!-- Botones -->
						<ul class="nav navbar-nav navbar-right">
							<li>
								<a href="#">
									Espacio Utilizado:
									<span class="badge">
										<?php
											echo(round(($pesoUsado / 1024 / 1024),2).' MB<br>');
										?>
									</span>
								</a>
							</li>

							<li>
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
										<li><a href="administracion.php">Usuarios</a></li>
										<li><a href="administracionArchivos.php">Archivos</a></li>
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
									<li><a href="logout.php">Cerrar Sesión</a></li>
								</ul>
							</li>
						</ul>
					</div>
					<?php
						}
					?>
				</div>
			</nav>
		</header>

		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<section class="main container col-sm-6 col-sm-offset-3">
			<div class="panel panel-danger">
				<div class="panel-heading text-center"><h3>Error</h3></div>
				
				<div class="panel-body text-center">
					<h4><?php echo($_SESSION['error']);?></h4>
						<?php unset($_SESSION['error']); ?>
				</div>
				
				<div class="panel-footer text-center">
					<p>
						<a class="btn btn-primary btn-md" href="../index.php">Ir a la p&aacute;gina principal</a>
					</p>
					
				</div>
			</div>

		</section>

		<!-- Footer -->
		<footer class="jumbotron navbar-fixed-bottom" id="jumbotronFooter">
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

		<script src="../js/jquery-2.1.3.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>

