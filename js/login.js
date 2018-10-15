//Funcion vacio
function vacio(campo, span, input){
	if (campo == ''){
		span.style.color= 'red';
		span.innerHTML= 'campo vacio';
		input.className= 'form-group has-error';
		resultado= false;
	}else{
		span.innerHTML= '';
		input.className= 'form-group has-success';
	}
}

/***** Validar *****/
function controlDeLogin(){
	resultado= true;

	vacio(document.getElementById('usuarioLogin').value, document.getElementById('spanUsuarioLogin'),document.getElementById('inputUsuarioLogin'));
	vacio(document.getElementById('claveLogin').value, document.getElementById('spanClaveLogin'),document.getElementById('inputClaveLogin'));	
	return resultado;
}