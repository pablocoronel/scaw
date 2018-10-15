<?php
	session_start();
	include_once 'conexionDB.php';

	//Evitar ingreso sin login
	if (!isset($_SESSION['loginId'])){
		header('location: ingreso.php');
		exit();
	}
	//Evitar ingreso sin ser administrador
	if (!isset($_SESSION['loginNivel']) || ($_SESSION['loginNivel'] != 'administrador')){
		header('location: ../index.php');
		exit();
	}
	//Desloguear si no es administrador
	$sqlAdmin= "SELECT nivel FROM usuario WHERE idUsuario = '".$_SESSION['loginId']."'";
	$resAdmin= mysqli_query($conexion,$sqlAdmin);
	$filaAdmin= mysqli_fetch_assoc($resAdmin);
	if ($filaAdmin['nivel'] != 'administrador') {
		session_destroy();
		header('location: ../index.php');
		exit();
	}
	/*************************************************/
	$sqlUsuario= "SELECT * FROM usuario WHERE estadoRegistro = 0 OR estadoRegistro = 1";
	$resUsuario= mysqli_query($conexion,$sqlUsuario);
	/*************************************************/
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
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/layout.css">
		<link rel="stylesheet" href="../css/administracion.css">

		<title>Administracion - TP SCAW</title>
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
								<li class="dropdown active">
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

		<section class="main container" id="contenidoPrincipal">
			<div class="page-header">
				<h1>Panel de Administracion: <small>Usuarios</small></h1>
			</div>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-hover">
					<tr class="info">
						<th>ID</th>
						<th>Usuario</th>
						<th>Nivel</th>
						<th>Espacio Asignado</th>
						<th>Estado de la cuenta</th>
						<th>Guardar Cambios</th>
						<th>Eliminar Usuario</th>
					</tr>
			<?php
				while ($filaUsuario= mysqli_fetch_assoc($resUsuario)) {
				//Estado registro
					$estadoRegistro='';
					if ($filaUsuario['estadoRegistro'] == 0) {
						$estadoRegistro= 'Cuenta NO habilitada';
					}elseif ($filaUsuario['estadoRegistro'] == 1) {
						$estadoRegistro= 'Cuenta habilitada';
					}
				//Espacio asignado
					$espacioAsignado= ($filaUsuario['espacioAsignado'] /1024/1024);
			?>
				<form action="administracionGuardar.php" method="post">
					<tr>
						<td><?php echo($filaUsuario['idUsuario']); ?></td>
						<td><?php echo($filaUsuario['usuario']); ?></td>
						<td>
							<select name="nivel" id="nivel">
								<option value="<?php echo($filaUsuario['nivel']); ?>"><?php echo($filaUsuario['nivel']); ?></option>
								<option value="usuario">Usuario</option>
								<option value="administrador">Administrador</option>
							</select>
							
						</td>
						<td>
							<?php
								echo($espacioAsignado.' MB');
								if ($filaUsuario['nivel'] == 'administrador') {
							?>
								<input type="text" name="espacioAdministrador" id="espacioAdministrador" placeholder="Modificar (en MB)"/>
							<?php
								}
							?>
						</td>
						<td>
							<select name="estadoRegistro" id="estadoRegistro">
								<option value="<?php echo($filaUsuario['estadoRegistro']); ?>"><?php echo($estadoRegistro); ?></option>
								<option value="0">Cuenta NO habilitada</option>
								<option value="1">Cuenta habilitada</option>
							</select>
						</td>
						
						<input type="hidden" name="idUsuario" value="<?php echo($filaUsuario['idUsuario']); ?>">

						<td><input type="submit" name="guardar" id="guardar" class="btn btn-success" value="Guardar"/></td>
						<td><input type="submit" name="eliminar" id="eliminar" class="btn btn-danger" value="Eliminar"/></td>
					</tr>
				</form>
			<?php
				}
			?>
				</table>
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