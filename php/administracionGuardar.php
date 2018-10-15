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
	/******************************************/
	//Limpiar
	$postIdUsuario= mysqli_real_escape_string($conexion,$_POST['idUsuario']);
	$postEstadoRegistro= mysqli_real_escape_string($conexion,$_POST['estadoRegistro']);
	$postNivel= mysqli_real_escape_string($conexion,$_POST['nivel']);
	if (isset($_POST['espacioAdministrador']) && (!empty($_POST['espacioAdministrador']))) {
		$postEspacioAdministrador= mysqli_real_escape_string($conexion,$_POST['espacioAdministrador']);
	}
	if (isset($_POST['guardar'])) {
		$postGuardar= mysqli_real_escape_string($conexion,$_POST['guardar']);
	}
	if (isset($_POST['eliminar'])){
		$postEliminar= mysqli_real_escape_string($conexion,$_POST['eliminar']);
	}
	/**********************************/
	//Guardar cambios:
	if (isset($postGuardar)) {
		if (isset($postEspacioAdministrador)) {
			$sqlGuardarAdm= "UPDATE usuario SET nivel = '".$postNivel."', espacioAsignado = '".($postEspacioAdministrador *1024*1024)."', estadoRegistro = '".$postEstadoRegistro."' WHERE idUsuario = '".$postIdUsuario."'";
			mysqli_query($conexion,$sqlGuardarAdm);
			header('location: administracion.php');
			exit();
		}else{
			$sqlGuardarUsr= "UPDATE usuario SET nivel = '".$postNivel."', estadoRegistro = '".$postEstadoRegistro."' WHERE idUsuario = '".$postIdUsuario."'";
			mysqli_query($conexion,$sqlGuardarUsr);
			header('location: administracion.php');
			exit();
		}
	}elseif (isset($postEliminar)) {
		$sqlEliminar= "UPDATE usuario SET estadoRegistro = 2 WHERE idUsuario = '".$postIdUsuario."'";
			mysqli_query($conexion,$sqlEliminar);
			header('location: administracion.php');
			exit();
	}else{
		$_SESSION['error']= 'No tienes acceso';
		header('location: error.php');
		die();
	}
?>