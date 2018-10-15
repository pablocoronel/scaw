<?php
	session_start();
	include_once 'conexionDB.php';

	//Limpiar
	$getCodigoArchivo= mysqli_real_escape_string($conexion,$_GET['cod']);
	//Si no hay codigo del archivo
	if (!isset($getCodigoArchivo) || empty($getCodigoArchivo)) {
		$_SESSION['error']= 'No tienes acceso';
		header('location: error.php');
		exit();
	}
	/*********************/
	//SQL ID de archivo:
	$sqlIdArchivo= "SELECT idArchivo, versionActual FROM archivo WHERE codigoArchivo = '".$getCodigoArchivo."'";
	$resIdArchivo= mysqli_query($conexion,$sqlIdArchivo);
	$filaIdArchivo= mysqli_fetch_assoc($resIdArchivo);
	//ID de archivo:
	$idArchivo= $filaIdArchivo['idArchivo'];
	//Si no hay archivo
	if(!$idArchivo){
		$_SESSION['error']= 'No tienes acces0o';
		header('location: error.php');
		exit();
	}
	//Version actual:
	$versionActual= $filaIdArchivo['versionActual'];
	//Nombre del archivo (por version):
	$sqlNombreVersion= "SELECT ruta FROM archivo_version WHERE fkArchivo = '".$idArchivo."' AND version = '".$versionActual."'";
	$resNombreVersion= mysqli_query($conexion,$sqlNombreVersion);
	$filaNombreVersion= mysqli_fetch_assoc($resNombreVersion);
	//Nombre del archivo en la version actual:
	$nombreArchivo= $filaNombreVersion['ruta'];
	//Carpetas
	$carpeta= '../files/archivo_'.$idArchivo.'/version_'.$versionActual.'/';
	//Archivo
	$archivo = basename($nombreArchivo);
	//Ruta
	$ruta = $carpeta.$archivo;
	//Tipo
	$tipo = '';
	  
	if (is_file($ruta)) {
		$peso = filesize($ruta);
		if (function_exists('mime_content_type')) {
			$tipo = mime_content_type($ruta);
		}else if(function_exists('finfo_file')) {
			$info = finfo_open(FILEINFO_MIME);
			$tipo = finfo_file($info, $ruta);
			finfo_close($info);
		}

		if ($tipo == '') {
			$tipo = "application/force-download";
		}
		// Definir headers
		header("Content-Type: $tipo");
		header("Content-Disposition: attachment; filename=$archivo");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $peso);
		// Descargar archivo
		readfile($ruta);
	}else{
		$_SESSION['error']= 'No existe el archivo';
		header('location: error.php');
		exit();
	}
?>