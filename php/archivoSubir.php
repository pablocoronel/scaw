<?php
	session_start();
	include_once 'conexionDB.php';

	//Evitar ingreso sin login
	if (!isset($_SESSION['loginId'])){
		header('location: php/ingreso.php');
		exit();
	}
	//Limpiar:
	$postSubir= mysqli_real_escape_string($conexion,$_POST['subir']);
	$postTipoCompartido= $_POST['tipoCompartido'];
	if (isset($_POST['listaEditores'])) {
		$postListaEditores= $_POST['listaEditores'];
	}
	if (isset($_POST['listaLectores'])) {
		$postListaLectores= $_POST['listaLectores'];
	}

	/*************** IF PRINCIPAL ****************/
	if ((isset($postSubir))  && (isset($postTipoCompartido))) {
		/************** IF DEL ARCHIVO ****************/
		//Archivo temporal:
		$archivoTemporal= $_FILES['archivo']['tmp_name'];
		if (file_exists($archivoTemporal)){
			//Nombre del archivo
			$archivoNombre= $_FILES['archivo']['name'];
			/********** PESOS Y ESPACIOS **********/
			//Peso del archivo:
			$archivoPeso= $_FILES['archivo']['size'];
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
					//Compartido publico o privado:
					foreach ($postTipoCompartido as $key => $value) {
						$tipoCompartido= $value;
					}
					//ID del archivo:
					$sql= "SELECT MAX(idArchivo) as idArchivo FROM archivo";
					$res= mysqli_query($conexion,$sql);
					$MayorIdArchivo= mysqli_fetch_assoc($res);
					$idArchivo= ($MayorIdArchivo['idArchivo'] +1);
					//CODIGO del archivo:
					$codigoArchivo='';
					for($i=1; $i <=8; $i++){
						switch(rand(1,2)){
							case 1: $codigoArchivo= $codigoArchivo.chr(rand(97,122));
								break;
							case 2: $codigoArchivo= $codigoArchivo.(rand(0,9));
								break;
						}
					}
					//Cantidad de archivos:
					$resCantidadArchivos= mysqli_query($conexion,"SELECT * FROM archivo");	
					$cantidadArchivos= mysqli_num_rows($resCantidadArchivos);
			
					/************** IF DE CARPETAS ****************/
					if ($cantidadArchivos > 0) {
						//Carpeta del archivo:
						$carpetaArchivo= 'archivo_'.$idArchivo;
						if (!file_exists('../files/'.$carpetaArchivo)) {
							mkdir('../files/'.$carpetaArchivo);
						}
						//Carpeta de la version del archivo:
						$carpetaVersion= 'version_1';
						//Ruta del archivo:
						$rutaArchivo= '../files/'.$carpetaArchivo.'/'.$carpetaVersion;
						if (!file_exists($rutaArchivo)) {
							mkdir($rutaArchivo);
						}
					}else{
						//Carpeta del archivo:
						$carpetaArchivo= '../files/archivo_1';
						if (!file_exists($carpetaArchivo)) {
							mkdir($carpetaArchivo);
						}
						//Carpeta de la version del archivo:
						$carpetaVersion= 'version_1';
						//Ruta del archivo:
						$rutaArchivo= '../files/archivo_1/'.$carpetaVersion;
						if (!file_exists($rutaArchivo)) {
							mkdir($rutaArchivo);
						}
					}
					/************ IF DE SUBIDA DEL ARCHIVO ******************/
					if(move_uploaded_file($archivoTemporal, $rutaArchivo.'/'.$archivoNombre)){
						/**************** DATOS DEL ARCHIVO ******************/
						//Tabla archivo:
						mysqli_query($conexion,"INSERT INTO archivo (nombreArchivo, tipoCompartido, versionActual, fkUsuarioCreador, codigoArchivo) VALUES ('".$archivoNombre."', '".$tipoCompartido."', '1', '".$_SESSION['loginId']."', '".$codigoArchivo."')");
						//Tabla archivo_version:
						mysqli_query($conexion,"INSERT INTO archivo_version (fkArchivo, version, ruta, peso) VALUES ('".$idArchivo."', '1', '".$rutaArchivo."/".$archivoNombre."', '".$archivoPeso."')");
						/************** ROLES EDITOR Y LECTOR **************/
						//Editor por ser creador:
						$sqlEditorCreador= "INSERT INTO usuario_editor (fkArchivo, fkUsuario) VALUES ('".$idArchivo."', '".$_SESSION['loginId']."')";
						mysqli_query($conexion,$sqlEditorCreador);
						//Editor por ListaEditores:
						if (isset($postListaEditores)) {
							foreach ($postListaEditores as $key => $value) {
								$sqlEditorLista= "INSERT INTO usuario_editor (fkArchivo, fkUsuario) VALUES ('".$idArchivo."', '".$value."')";
								mysqli_query($conexion,$sqlEditorLista);
							}
						}
						/***** Solo compartido en privado *****/
						$sqlPrivado= "SELECT tipoCompartido 
										FROM archivo
											WHERE idArchivo = ".$idArchivo;
						$resPrivado= mysqli_query($conexion,$sqlPrivado);
						$filaPrivado= mysqli_fetch_assoc($resPrivado);
						if ($filaPrivado['tipoCompartido'] == 'privado') {	
							//Lector por ser creador:
							$sqlLectorCreador= "INSERT INTO usuario_lector (fkArchivo, fkUsuario) VALUES ('".$idArchivo."', '".$_SESSION['loginId']."')";
							mysqli_query($conexion,$sqlLectorCreador);
							//Lector por ListaLectores:
							if (isset($postListaLectores)) {
								foreach ($postListaLectores as $key => $value) {
									$sqlLectorLista= "INSERT INTO usuario_lector (fkArchivo, fkUsuario) VALUES ('".$idArchivo."', '".$value."')";
									mysqli_query($conexion,$sqlLectorLista);
								}
							}
						}
						header('location: ../index.php');
						exit();
					}else{
						$_SESSION['error']= 'No se pudo subir el archivo';
						header('location: error.php');
						exit();
					}
				}else{
					$_SESSION['error']= 'No queda espacio disponible para el archivo';
					header('location: error.php');
					exit();
				}
				
			}else{
				$_SESSION['error']= 'No es un archivo permitido';
				header('location: error.php');
				exit();
			}

		}else{
			$_SESSION['error']= 'No hay un archivo seleccionado';
			header('location: error.php');
			exit();
		}
	}else{
		$_SESSION['error']= 'sin acceso';
		header('location: error.php');
		exit();
	}
?>