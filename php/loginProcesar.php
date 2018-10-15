<?php
	session_start();
	include_once 'conexionDB.php';

	//Limpiar
	$postUsuarioLogin= mysqli_real_escape_string($conexion,$_POST['usuarioLogin']);
	$postClaveLogin= mysqli_real_escape_string($conexion,$_POST['claveLogin']);	
	//Loguear
	if(isset($postUsuarioLogin) && (isset($postClaveLogin))){
		
		$pass= md5($postClaveLogin);
		$sql= "SELECT * FROM usuario
					WHERE usuario= '$postUsuarioLogin' AND clave= '".$pass."'";
		$res= mysqli_query($conexion,$sql);
		$fila= mysqli_fetch_assoc($res);

		if (!empty($fila)) {
			if ($fila['estadoRegistro'] == 1) {
				$_SESSION['loginId']= $fila['idUsuario'];
				$_SESSION['loginNivel']= $fila['nivel'];
				$_SESSION['loginUsuario']= $fila['usuario'];
				header('location: ../index.php');
				exit();
			}elseif($fila['estadoRegistro'] == 0) {
				$_SESSION['error']= 'Usuario no habilitado';
				header('location: ingreso.php');
				exit();
			}elseif ($fila['estadoRegistro'] == 2) {
				$_SESSION['error']= 'Usuario eliminado';
				header('location: ingreso.php');
				exit();
			}else{
				$_SESSION['error']= 'Error al intentar ingresar';
				header('location: ingreso.php');
				exit();
			}
		}else{
			$_SESSION['error']= 'Error al intentar ingresar';
			header('location: ingreso.php');
			exit();
		}
	}else{
		$_SESSION['error']= 'Error al intentar ingresar';
		header('location: ingreso.php');
		exit();
	}
?>