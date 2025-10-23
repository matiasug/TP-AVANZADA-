
class FormLogin {
    constructor() {
        this.form = document.getElementById('formulario1');
    }

    validoInputsIndividual() {
        let formValido = true;

        // --- Nombre ---
        const nombre = document.getElementById('nombre');
        let errorNombre = nombre.parentNode.querySelector('.error');
        if (!errorNombre) {
            errorNombre = document.createElement('span');
            errorNombre.classList.add('error');
            nombre.parentNode.appendChild(errorNombre);
        }
        errorNombre.textContent = '';

        if (nombre.value.trim() === '' || nombre.value.trim().length < 3) {
            nombre.classList.add('is-invalid');
            nombre.classList.remove('is-valid');
            errorNombre.textContent = 'Por favor, ingrese su nombre completo.';
            formValido = false;
        } else {
            nombre.classList.add('is-valid');
            nombre.classList.remove('is-invalid');
        }

        // --- Correo ---
        const correo = document.getElementById('correo');
        let errorCorreo = correo.parentNode.querySelector('.error');
        if (!errorCorreo) {
            errorCorreo = document.createElement('span');
            errorCorreo.classList.add('error');
            correo.parentNode.appendChild(errorCorreo);
        }
        errorCorreo.textContent = '';

        const correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]{2,4}$/.test(correo.value.trim());
        if (correo.value.trim() === '' || !correoValido) {
            correo.classList.add('is-invalid');
            correo.classList.remove('is-valid');
            errorCorreo.textContent = 'Por favor, ingresa un correo válido.';
            formValido = false;
        } else {
            correo.classList.add('is-valid');
            correo.classList.remove('is-invalid');
        }

        // --- Contraseña ---
      const contrasena = document.getElementById('cont');
        let errorContrasena = contrasena.parentNode.querySelector('.error');
        if (!errorContrasena) {
            errorContrasena = document.createElement('span');
            errorContrasena.classList.add('error');
            contrasena.parentNode.appendChild(errorContrasena);
        }
        errorContrasena.textContent = '';

        const passwordValida = /^[a-zA-Z0-9]{6,20}$/.test(contrasena.value.trim());
        if (contrasena.value.trim() === '' || !passwordValida) {
            contrasena.classList.add('is-invalid');
            contrasena.classList.remove('is-valid');
            errorContrasena.textContent = 'La contraseña debe tener entre 6 y 20 caracteres (solo letras y números).';
            formValido = false;
        } else {
            contrasena.classList.add('is-valid');
            contrasena.classList.remove('is-invalid');
        }
        let psswHasheada = sha512(passwordValida);
        console.log(psswHasheada);//pa 
        //si la contraseña pasa todas estas pruebas, aca la hasheas con sha512 - uno de los algoritmos que mas se usa
        
        return formValido;
    }
}

// Inicialización
document.addEventListener("DOMContentLoaded", function() {
    const formLogin = new FormLogin();

    formLogin.form.addEventListener('submit', function(event) {
        if (!formLogin.validoInputsIndividual()) {
            event.preventDefault(); // Evita enviar si hay errores
        }
        //aca deberian hacer un form oculto para mandar los datos
        var form = new FormData();
        const nombre = document.getElementById('nombre');
        form.append(nombreF, nombre);//nombre del campo del form - la variable que contiene el valor
        //asi van haciendo con los demas datos
        //le hacen form.submit();
    });
});
