//Funcion usuario
function usuario(campo, span, input){
	formato= /^[\w-_]{2,20}$/;

	if(campo == ''){
		resultado= false;
		span.style.color= 'red';
		span.innerHTML= 'Usuario incorrecto';
		input.className= 'form-group has-error';
		}else if(!campo.match(formato)){
			resultado= false;
			span.style.color= 'red';
			span.innerHTML= 'Usuario incorrecto';
			input.className= 'form-group has-error';
		}else{
			span.innerHTML= '';
			input.className= 'form-group has-success';
		}
}
//Funcion contraseña
function clave(campo, span, input){
	formato= /^[\w-_\.]{6,20}$/;
	
	if(campo == ''){
		resultado= false;
		span.style.color= 'red';
		span.innerHTML= 'Clave incorrecta';
		input.className= 'form-group has-error';
		}else if(!campo.match(formato)){
			resultado= false;
			span.style.color= 'red';
			span.innerHTML= 'Clave incorrecta';
			input.className= 'form-group has-error';
		}else{
			span.innerHTML= '';
			input.className= 'form-group has-success';
		}
}

/***** Validar y guardar *****/
function controlDeRegistro(){
	resultado= true;

	usuario(document.getElementById('usuarioRegistro').value,document.getElementById('spanUsuarioRegistro'),document.getElementById('inputUsuarioRegistro'));
	clave(document.getElementById('claveRegistro').value,document.getElementById('spanClaveRegistro'),document.getElementById('inputClaveRegistro'));
	
	return resultado;
}