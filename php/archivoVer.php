<?php
	session_start();
	include_once 'conexionDB.php';

	//Limpar:
	$getCod= mysqli_real_escape_string($conexion,$_GET['cod']);
	//Evitar ingreso sin codigo de archivo
	if (!isset($getCod)){
		header('location: ingreso.php');
		exit();
	}elseif (empty($getCod)) {
		$_SESSION['error']= 'No tienes acceso';
		header('location: error.php');
		exit();
	}
	/**************/
	//SQL ID de archivo:
	$sqlIdArchivo= "SELECT idArchivo FROM archivo WHERE codigoArchivo = '".$getCod."'";
	$resIdArchivo= mysqli_query($conexion,$sqlIdArchivo);
	$filaIdArchivo= mysqli_fetch_assoc($resIdArchivo);
	//ID de archivo:
	$idArchivo= $filaIdArchivo['idArchivo'];
	/*********************/
	//Lista de versiones:
	$sqlVersiones= "SELECT archivo_version.version as verArch, 
					archivo.nombreArchivo as nomArch 
		FROM archivo_version INNER JOIN archivo 
		ON archivo_version.fkArchivo = archivo.idArchivo 
		WHERE archivo_version.fkArchivo = ".$idArchivo;
	$respuestaVersiones= mysqli_query($conexion,$sqlVersiones);
	//Version actual:
	$sqlVersionActual= "SELECT versionActual, nombreArchivo, codigoArchivo FROM archivo WHERE idArchivo = ".$idArchivo;
	$respuestaVersionActual= mysqli_query($conexion,$sqlVersionActual);
	$filaVersionActual= mysqli_fetch_assoc($respuestaVersionActual);
	/********************/
	//Peso de la version actual del archivo:
	$sqlPesoVersionActual= "SELECT archivo_version.peso as pesoAct FROM archivo_version INNER JOIN archivo
								ON archivo_version.version= archivo.versionActual
									WHERE archivo_version.fkArchivo= ".$idArchivo;
	$respuestaPesoVersionActual= mysqli_query($conexion,$sqlPesoVersionActual);
	$filaPesoVersionActual= mysqli_fetch_assoc($respuestaPesoVersionActual);
	/*******************/
	//Ver espacio utilizado y disponible:
	//Peso utilizado:
	if(isset($_SESSION['loginId'])){
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
	/****************************************************************/
	//SQL de tipo compartido
	$sqlTipoCompartido= "SELECT tipoCompartido FROM archivo
							WHERE idArchivo = ".$idArchivo;
	$resTipoCompartido= mysqli_query($conexion,$sqlTipoCompartido);
	$filaTipoCompartido= mysqli_fetch_assoc($resTipoCompartido);
	//SQL lista de lectores de archivo:
	$sqlListaLectoresPrivado= "SELECT fkUsuario FROM usuario_lector
								WHERE fkArchivo = ".$idArchivo;
	$resListaLectoresPrivado= mysqli_query($conexion,$sqlListaLectoresPrivado);
	//SQL lista de editores de archivo:
	$sqlListaEditoresPrivado= "SELECT fkUsuario FROM usuario_editor
								WHERE fkArchivo = ".$idArchivo;
	$resListaEditoresPrivado= mysqli_query($conexion,$sqlListaEditoresPrivado);
	/*************/
	//Lista de usuarios lectores:
	$lectorPermitido= false;
	if(isset($_SESSION['loginId'])){
		while ($filaListaLectoresPrivado= mysqli_fetch_assoc($resListaLectoresPrivado)) {
			if ($filaListaLectoresPrivado['fkUsuario'] == $_SESSION['loginId']) {
				$lectorPermitido= true;
			}
		}
	}
	//Lista de usuarios editores:
	$editorPermitido= false;
	if(isset($_SESSION['loginId'])){
		while ($filaListaEditoresPrivado= mysqli_fetch_assoc($resListaEditoresPrivado)) {
			if ($filaListaEditoresPrivado['fkUsuario'] == $_SESSION['loginId']) {
				$editorPermitido= true;
			}
		}
	}
	//Acceso para administrador:
	if (isset($_SESSION['loginNivel']) && ($_SESSION['loginNivel'] == 'administrador')) {
		$editorPermitido= true;
	}
/***************************************************************************/
	//Verificar permiso en archivo publico y privado:
	if ($filaTipoCompartido['tipoCompartido'] == 'privado') {
		if (!isset($_SESSION['loginId'])) {
			$_SESSION['error']= 'No tienes acceso al archivo';
			header('location: error.php');
			exit();
		}elseif ($editorPermitido == true) {
			//Usuario en lista de  editores privado
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">
		<link rel="stylesheet" href="../css/verArchivo.css">

		<title>Ver archivo - TP SCAW</title>
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
				</div>
			</nav>
		</header>

		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<section id="contenido" class="main container">
			<!-- archivo privado - usuario editor
			<hr> 
			 -->
			<div class="row">
				<!-- Descargar y subir version -->
				<section class="col-md-8">
					<!-- Descargar -->
					<div id="divVersionActual" class="col-md-12">
						<div class="col-md-8 col-md-offset-2">
							<div class="alert alert-success text-center">
								<a class="btn btn-primary" href="archivoDescargar.php?cod=<?php echo($filaVersionActual['codigoArchivo']); ?>">
									Descargar Archivo									
								</a>
								<p>
									Archivo: <?php echo($filaVersionActual['nombreArchivo']); ?>
								</p>
								<p>
									Version: <?php echo($filaVersionActual['versionActual']); ?>
								</p>
								<p>
									Peso: <?php echo(round($filaPesoVersionActual['pesoAct'] / 1024/1024,3).' MB'); ?>
								</p>
							</div>
						</div>
					</div>

					<!-- Subir nueva version -->
					<div class="col-md-12">
						<form action="archivoVersionSubir.php" method="post" enctype='multipart/form-data'>
							<div class="panel panel-primary text-center">
								<div class="panel-heading">
									<b>Subir nueva version para el archivo</b>
								</div>
								
								<div class="panel-body text-center">
									<div class="col-md-6 col-md-offset-3">
										<input type="file" name="nuevaVersion" id="nuevaVersion">
									</div>
									<input type="hidden" name="codArchivoModificar" id="codArchivoModificar" value="<?php echo($getCod); ?>">
								</div>
								
								<!-- Boton subir archivo -->
								<div class="panel-body">
									<button class="btn btn-success" type="submit" value="Subir Nueva Version" name="subirVersion" id="subirVersion">Subir Nueva Version</button>
								</div>
							</div>
						</form>
					</div>
				</section>
				
				<!-- Versiones historicas -->
				<aside class="col-md-4">
					<!-- Versiones historicas -->
					<div id="divVersiones">
						<form action="archivoRestaurarVersion.php" method="post">
							<div class="list-group">
								<div class="list-group-item list-group-item-info text-left">
									<b>Versiones historicas</b>
								</div>

							<?php					
								while($filaVersiones=mysqli_fetch_assoc($respuestaVersiones)){
							?>
								<div class="list-group-item text-left">
									<input type="radio" name="listaVersiones[]" id="listaVersiones[]" value="<?php echo($filaVersiones['verArch']); ?>">
									<?php echo($filaVersiones['nomArch'].' - '.$filaVersiones['verArch']); ?>
								</div>
							<?php
								}
							?>
								<input type="hidden" name="codArchivoCentral" id="codArchivoCentral" value="<?php echo($getCod); ?>">
								
								<!-- Boton subir archivo -->
								<div class="list-group-item text-center">
									<button class="btn btn-success" type="submit" value="Restaurar Version" name="restaurarVersion" id="restaurarVersion">Restaurar Version</button>
								</div>
							</div>
						</form>
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

		<script src="../js/jquery-2.1.3.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>
<?php
		}elseif ($lectorPermitido == true) {
			//Usuario en lista de lectores privado
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">
		<link rel="stylesheet" href="../css/verArchivo.css">

		<title>Ver archivo - TP SCAW</title>
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
				</div>
			</nav>
		</header>

		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<section id="contenido" class="main container">
			<!-- archivo privado - usuario lector
			<hr>
			 -->
			<div class="row">
				<!-- Descargar -->
				<section class="col-md-8">
					<!-- Descargar -->
					<div id="divVersionActual" class="col-md-12">
						<div class="col-md-8 col-md-offset-2">
							<div class="alert alert-success text-center">
								<a class="btn btn-primary" href="archivoDescargar.php?cod=<?php echo($filaVersionActual['codigoArchivo']); ?>">
									Descargar Archivo
								</a>
								<p>
									Archivo: <?php echo($filaVersionActual['nombreArchivo']); ?>
								</p>
								<p>
									Version: <?php echo($filaVersionActual['versionActual']); ?>
								</p>
								<p>
									Peso: <?php echo(round($filaPesoVersionActual['pesoAct'] / 1024/1024,3).' MB'); ?>
								</p>
							</div>
						</div>
					</div>
				</section>
				
				<!-- Versiones historicas -->
				<aside class="col-md-4">
					<!-- Versiones historicas -->
					<div id="divVersiones">
						<div class="list-group">
							<div class="list-group-item list-group-item-info text-left">
								<b>Versiones historicas</b>
							</div>
						<?php					
							while($filaVersiones=mysqli_fetch_assoc($respuestaVersiones)){
						?>
							<div class="list-group-item text-left">
								<?php echo($filaVersiones['nomArch'].' - '.$filaVersiones['verArch']); ?>
							</div>
						<?php
							}
						?>
						</div>
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

		<script src="../js/jquery-2.1.3.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>
<?php
		}elseif($lectorPermitido == false) {
			$_SESSION['error']= 'No tienes acceso al archivo';
			header('location: error.php');
			exit();
		}elseif($editorPermitido == false) {
			$_SESSION['error']= 'No tienes acceso al archivo';
			header('location: error.php');
			exit();
		}
	}elseif ($filaTipoCompartido['tipoCompartido'] == 'publico'){
		if (isset($_SESSION['loginId'])){
			if ($editorPermitido == true) {
				//Usuario logueado en lista de editor
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">
		<link rel="stylesheet" href="../css/verArchivo.css">

		<title>Ver archivo - TP SCAW</title>
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
				</div>
			</nav>
		</header>

		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<section id="contenido" class="main container">
			<!-- archivo publico - usuario editor
			<hr> -->
			<div class="row">
				<!-- Descargar y subir version -->
				<section class="col-md-8">
					<!-- Descargar -->
					<div id="divVersionActual" class="col-md-12">
						<div class="col-md-8 col-md-offset-2">
							<div class="alert alert-success text-center">
								<a class="btn btn-primary" href="archivoDescargar.php?cod=<?php echo($filaVersionActual['codigoArchivo']); ?>">
									Descargar Archivo
								</a>
								<p>
									Archivo: <?php echo($filaVersionActual['nombreArchivo']); ?>
								</p>
								<p>
									Version: <?php echo($filaVersionActual['versionActual']); ?>
								</p>
								<p>
									Peso: <?php echo(round($filaPesoVersionActual['pesoAct'] / 1024/1024,3).' MB'); ?>
								</p>
							</div>
						</div>
					</div>

					<!-- Subir nueva version -->
					<div class="col-md-12">
						<form action="archivoVersionSubir.php" method="post" enctype='multipart/form-data'>
							<div class="panel panel-primary text-center">
								<div class="panel-heading">
									<b>Subir nueva version para el archivo</b>
								</div>

								<div class="panel-body text-center">
									<div class="col-md-6 col-md-offset-3">
										<input type="file" name="nuevaVersion" id="nuevaVersion">
									</div>
									<input type="hidden" name="codArchivoModificar" id="codArchivoModificar" value="<?php echo($getCod); ?>">
								</div>
								
								<!-- Boton subir archivo -->
								<div class="panel-body">
									<button class="btn btn-success" type="submit" value="Subir Nueva Version" name="subirVersion" id="subirVersion">Subir Nueva Version</button>
								</div>
							</div>
						</form>
					</div>
				</section>
			
				<!-- Versiones historicas -->
				<aside class="col-md-4">
					<!-- Versiones historicas -->
					<div id="divVersiones">
						<form action="archivoRestaurarVersion.php" method="post">
							<div class="list-group">
								<div class="list-group-item list-group-item-info text-left">
									<b>Versiones historicas:</b>
								</div>
							
							<?php					
								while($filaVersiones=mysqli_fetch_assoc($respuestaVersiones)){
							?>
							<div class="list-group-item text-left">
								<input type="radio" name="listaVersiones[]" id="listaVersiones[]" value="<?php echo($filaVersiones['verArch']); ?>">
								<?php echo($filaVersiones['nomArch'].' - '.$filaVersiones['verArch']); ?>
							</div>						
							<?php
								}
							?>
								<input type="hidden" name="codArchivoCentral" id="codArchivoCentral" value="<?php echo($getCod); ?>">
						
								<!-- Boton subir archivo -->
								<div class="list-group-item text-center">
									<button class="btn btn-success" type="submit" value="Restaurar Version" name="restaurarVersion" id="restaurarVersion">Restaurar Version</button>
								</div>
							</div>
						</form>
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

		<script src="../js/jquery-2.1.3.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>
<?php
			}else{
				//Usuario logueado NO editor
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">
		<link rel="stylesheet" href="../css/verArchivo.css">

		<title>Ver archivo - TP SCAW</title>
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
				</div>
			</nav>
		</header>

		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<section id="contenido" class="main container">
			<!-- archivo publico - usuario sin permiso de editor
			<hr> --> 
			<div class="row">
				<!-- Descargar -->
				<section class="col-md-8">
					<!-- Descargar -->
					<div id="divVersionActual" class="col-md-12">
						<div class="col-md-8 col-md-offset-2">
							<div class="alert alert-success text-center">
								<a class="btn btn-primary" href="archivoDescargar.php?cod=<?php echo($filaVersionActual['codigoArchivo']); ?>">
									Descargar Archivo
								</a>
								<p>
									Archivo: <?php echo($filaVersionActual['nombreArchivo']); ?>
								</p>
								<p>
									Version: <?php echo($filaVersionActual['versionActual']); ?>
								</p>
								<p>
									Peso: <?php echo(round($filaPesoVersionActual['pesoAct'] / 1024/1024,3).' MB'); ?>
								</p>
							</div>
						</div>
					</div>
				</section>
				
				<!-- Versiones historicas -->
				<aside class="col-md-4">
					<!-- Versiones historicas -->
					<div id="divVersiones">
						<div class="list-group">
							<div class="list-group-item list-group-item-info text-left">
								<b>Versiones historicas:</b>
							</div>
						<?php					
							while($filaVersiones=mysqli_fetch_assoc($respuestaVersiones)){
						?>
							<div class="list-group-item text-left">
								<?php echo($filaVersiones['nomArch'].' - '.$filaVersiones['verArch']); ?>
							</div>
						<?php
							}
						?>
						</div>
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

		<script src="../js/jquery-2.1.3.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>
<?php
			}
		}else{
			//Usuario anonimo
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">
		<link rel="stylesheet" href="../css/verArchivo.css">

		<title>Ver archivo - TP SCAW</title>
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

					<!-- Cuerpo del Menu -->
					<!-- <div class="collapse navbar-collapse" id="navegacionProyectoFinal">
						
					</div> -->
					<div class="collapse navbar-collapse" id="navegacionProyectoFinal">
						<!-- Botones -->
						<ul class="nav navbar-nav navbar-right">
							<li class="active">
								<a href="ingreso.php">
									Ingresar / Registrarse
								</a>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</header>

		<section class="jumbotron" id="jumbotronPrincipal">
			<div class="container">
				<h1>TP scaw</h1>
			</div>
		</section>

		<section id="contenido" class="main container">
			<!-- archivo publico - usuario anonimo
			<hr> -->
			<div class="row">
				<!-- Descargar y subir version -->
				<section class="col-md-12">
					<!-- Descargar -->
					<div id="divVersionActual" class="col-md-8 col-md-offset-2">
						<div class="col-md-8 col-md-offset-2">
							<div class="alert alert-success text-center">
								<a class="btn btn-primary" href="archivoDescargar.php?cod=<?php echo($filaVersionActual['codigoArchivo']); ?>">
									Descargar Archivo
								</a>
								<p>
									Archivo: <?php echo($filaVersionActual['nombreArchivo']); ?>
								</p>
								<p>
									Peso: <?php echo(round($filaPesoVersionActual['pesoAct'] / 1024/1024,3).' MB'); ?>
								</p>
							</div>
						</div>
					</div>
				</section>
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

		<script src="../js/jquery-2.1.3.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>
<?php
		}
	}else{
		$_SESSION['error']= 'No tienes acceso al archivo';
		header('location: error.php');
		exit();
	}
?>