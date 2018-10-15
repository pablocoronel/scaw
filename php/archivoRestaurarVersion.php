<?php
	session_start();
	include_once 'conexionDB.php';

	//Evitar ingreso sin login
	if (!isset($_SESSION['loginId'])){
		header('location: php/ingreso.php');
		exit();
	}

	//Limpiar
	$postCodArchivoCentral= mysqli_real_escape_string($conexion,$_POST['codArchivoCentral']);
	$postRestaurarVersion= mysqli_real_escape_string($conexion,$_POST['restaurarVersion']);
	$postListaVersiones= $_POST['listaVersiones'];
	
	//SQL ID de archivo:
	$sqlIdArchivo= "SELECT idArchivo FROM archivo WHERE codigoArchivo = '".$postCodArchivoCentral."'";
	$resIdArchivo= mysqli_query($conexion,$sqlIdArchivo);
	$filaIdArchivo= mysqli_fetch_assoc($resIdArchivo);
	//ID de archivo:
	$idArchivo= $filaIdArchivo['idArchivo'];
/**********************************/	
	if(isset($postRestaurarVersion) && (isset($postListaVersiones))){
		if (!empty($postListaVersiones)) {
			foreach ($postListaVersiones as $key => $value) {
				$sqlRestaurarVersion= "UPDATE archivo SET versionActual= '".$value."' WHERE idArchivo = '".$idArchivo."'";
				mysqli_query($conexion,$sqlRestaurarVersion);
			}
			header('location: archivoVer.php?cod='.$postCodArchivoCentral);
			exit();
		}else{
			$_SESSION['error']= 'No hay version seleccionada';
			header('location: error.php');
			exit();
		}
	}else{
		$_SESSION['error']= 'No tienes permiso';
		header('location: error.php');
		exit();
	}
?>