<?php
	session_start();
	include_once 'conexionDB.php';

	//Evitar ingreso sin login
	if (!isset($_SESSION['loginId'])){
		header('location: php/ingreso.php');
		exit();
	}
	//Limpiar:
	$postSubirVersion= mysqli_real_escape_string($conexion,$_POST['subirVersion']);
	$postCod= mysqli_real_escape_string($conexion,$_POST['codArchivoModificar']);
	//SQL ID de archivo:
	$sqlIdArchivo= "SELECT idArchivo FROM archivo WHERE codigoArchivo = '".$postCod."'";
	$resIdArchivo= mysqli_query($conexion,$sqlIdArchivo);
	$filaIdArchivo= mysqli_fetch_assoc($resIdArchivo);
	//ID de archivo:
	$idArchivo= $filaIdArchivo['idArchivo'];
	/*************** IF PRINCIPAL ****************/
	if (isset($postSubirVersion)) {
		/************** IF DEL ARCHIVO ****************/
		//Archivo temporal:
		$archivoTemporal= $_FILES['nuevaVersion']['tmp_name'];
		if (file_exists($archivoTemporal)){
			//Nombre del archivo
			$archivoNombre= $_FILES['nuevaVersion']['name'];
			/********** PESOS Y ESPACIOS **********/
			//Peso del archivo:
			$archivoPeso= $_FILES['nuevaVersion']['size'];
			//Espacio asignado:
			$sqlEspacioAsignado= "SELECT espacioAsignado FROM usuario
									WHERE idUsuario = ".$_SESSION['loginId'];
			$resEspacioAsignado= mysqli_query($conexion,$sqlEspacioAsignado);
			$filaEspacioAsignado= mysqli_fetch_assoc($resEspacioAsignado);
			$espacioAsignado= $filaEspacioAsignado['espacioAsignado'];
			//Espacio utilizado
			$sqlPesoUsado= "SELECT SUM(peso) as peso, fkUsuarioCreador FROM archivo_version as av INNER JOIN archivo as ar
				ON av.fkArchivo = ar.idArchivo
				WHERE ar.fkUsuarioCreador = ".$_SESSION['loginId'];
			$resPesoUsado= mysqli_query($conexion,$sqlPesoUsado);
			$filaPesoUsado= mysqli_fetch_assoc($resPesoUsado);
			$pesoUsado= $filaPesoUsado['peso'];
			//Espacio disponible
			$espacioDisponible= $espacioAsignado - $pesoUsado;
			
			/********* ARCHIVO DE TIPO PERMITIDO  ***********/
			$extensionesPermitidas= array('jpeg', 'jpg', 'gif', 'bmp', 'png', 'psd',
											'txt', 'doc', 'docx', 'pdf', 'odt', 'ppt', 'pptx',
											 '7z', 'zip', 'rar');
			$buscarExtension= pathinfo($archivoNombre);
			$extensionArchivo= $buscarExtension['extension'];

			if(in_array(strtolower($extensionArchivo), $extensionesPermitidas)){
				/********* ARCHIVO CON PESO PERMITIDO  ***********/
				if ($archivoPeso <= $espacioDisponible) {
					//Cantidad de archivos:
					$resCantidadVersiones= mysqli_query($conexion,"SELECT COUNT(idArchivoVersion) as numVersiones FROM archivo_version WHERE fkArchivo = ".$idArchivo);	
					$filaCantidadVersiones= mysqli_fetch_assoc($resCantidadVersiones);
					$cantidadVersiones= $filaCantidadVersiones['numVersiones'];
					/************** IF DE CARPETAS ****************/
					if ($cantidadVersiones > 0) {
						//Carpeta del archivo:
						$carpetaArchivo= 'archivo_'.$idArchivo;
						if (!file_exists('../files/'.$carpetaArchivo)) {
							mkdir('../files/'.$carpetaArchivo);
						}
						//Carpeta de la version del archivo:
						$numeroVersion= ($cantidadVersiones + 1);
						$carpetaVersion= 'version_'.$numeroVersion;
						//Ruta del archivo:
						$rutaArchivo= '../files/'.$carpetaArchivo.'/'.$carpetaVersion;
						if (!file_exists($rutaArchivo)) {
							mkdir($rutaArchivo);
						}
					}else{
						$_SESSION['error']= 'No hay versiones anteriores';
						header('location: error.php');
						exit();
					}
					/************ IF DE SUBIDA DEL ARCHIVO ******************/
					if(move_uploaded_file($archivoTemporal, $rutaArchivo.'/'.$archivoNombre)){
						/**************** DATOS DEL ARCHIVO ******************/
						//Tabla archivo:
						mysqli_query($conexion,"UPDATE archivo SET versionActual = '".$numeroVersion."' WHERE idArchivo = '".$idArchivo."'");
						//Tabla archivo_version:
						mysqli_query($conexion,"INSERT INTO archivo_version (fkArchivo, version, ruta, peso) VALUES ('".$idArchivo."', '".$numeroVersion."', '".$rutaArchivo."/".$archivoNombre."', '".$archivoPeso."')");
						header('location: archivoVer.php?cod='.$postCod);
						exit();
					}else{
						$_SESSION['error']= 'No se pudo subir el archivo';
						header('location: error.php');
						exit();
					}
				}else{
					$_SESSION['error']= 'No queda espacio para el archivo';
					header('location: error.php');
					exit();
				}

			}else{
				$_SESSION['error']= 'No es un archivo permitido';
				header('location: error.php');
				exit();
			}

		}else{
			$_SESSION['error']= 'No hay una version para subir';
			header('location: error.php');
			exit();
		}
	}else{
		$_SESSION['error']= 'sin acceso';
		header('location: error.php');
		exit();
	}
?>