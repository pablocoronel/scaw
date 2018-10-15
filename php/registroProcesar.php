<?php
	session_start();
	include_once 'conexionDB.php';
	//Recaptcha:
	include_once 'recaptcha-1.0.0/php/recaptchalib.php';
	//Clave secreta:
	$secret= '6Lc9aAwTAAAAAHsM2l-6qQ0pK6VbImTtE1AR5EaO';
	//Respuesta vacia:
	$response= null;
	//Comprueba la clave secreta:
	$reCaptcha= new ReCaptcha($secret);

	//Limpiar:
	$postUsuarioRegistro= mysqli_real_escape_string($conexion,$_POST['usuarioRegistro']);
	$postClaveRegistro= mysqli_real_escape_string($conexion,$_POST['claveRegistro']);
	/***** Funciones *****/
	//Funcion usuario
	function usuario($conex, $campo){
		$formato= "/^[\w-_]{2,20}$/";
		$sql_usuarioEnDB="SELECT usuario FROM usuario WHERE usuario = '".$campo."'";
		$existeUsuario= mysqli_query($conex,$sql_usuarioEnDB);
		$cantidad_usuarioEnDB= mysqli_num_rows($existeUsuario);
		
		if($campo == ''){
			$resultado= false;
			}else if ($cantidad_usuarioEnDB > 0) {
				$resultado= false;
			}else if(!preg_match($formato, $campo)){
				$resultado= false;
			}else{
				$resultado= true;
			}
		return $resultado;
	}
	//Funcion contraseña
	function clave($campo){
		$formato= "/^[\w-_\.]{6,20}$/";
		
		if($campo == ''){
			$resultado= false;
			}else if(!preg_match($formato, $campo)){
				$resultado= false;
			}else{
				$resultado= true;
			}
		return $resultado;
	}
	
	//Comprobacion del envio de ReCaptcha:
	if ($_POST['g-recaptcha-response']) {
		$response= $reCaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $_POST['g-recaptcha-response']);
	}

	//Comprobacion de ReCaptcha correcto:
	if ($response != null && $response->success) {
		/***** Validar y guardar *****/	
		if((usuario($conexion, $postUsuarioRegistro) == false) || (clave($postClaveRegistro) == false)){
			$_SESSION['error']= 'Usuario no disponible';
			header('location: ingreso.php');
			exit();
		}else{
			//Espacio por defecto: 10MB (10240 KB o 10485760 B)
			$espacioAsignado= (10 * 1024 * 1024);

			$sql="INSERT INTO usuario (nivel, usuario, clave, espacioAsignado, estadoRegistro)
					VALUES ('usuario', '$postUsuarioRegistro','".md5($postClaveRegistro)."', '".$espacioAsignado."', '1')";
			mysqli_query($conexion,$sql) or die ("Error en el registro del usuario.".mysqli_error($conexion));
			$_SESSION['registroCorrecto']= 'El registro se realizo correctamente, ya puedes ingresar';
			header('location: ../index.php');
			exit();
		}
	}else{
		$_SESSION['error']= 'Captcha incorrecto';
		header('location: ingreso.php');
		exit();
	}
?>