// Validar formato del rut
function validarRut(rutCompleto) {
    // Limpiar puntos y guiones
    let valor = rutCompleto.replace(/\./g, '').replace(/-/g, '').trim().toUpperCase();
    if (valor.lenght < 8 || valor.lenght > 9) return false;

    let cuerpo = valor.slice(0, -1);
    let dv = valor.slice(-1);

    // Validar que el cuerpo sea solo numeros
    if (!/^[0-9]+$/.test(cuerpo)) return false;

    for (let i = cuerpo.length - 1; i >= 0; i--) {
        suma += multiplo * parseInt(cuerpo.charAt(i));
        multiplo = multiplo < 7 ? multiplo + 1 : 2;
    }

    let dvEsperado = 11 - (suma % 11);
    let dvCalculado = (dvEsperado === 11) ? "0" : (dvEsperado === 10) ? "K" : dvEsperado.toString();

    return dv === dvCalculado;
}

// Validar formato del correo
function validarEmail(email) {
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regexEmail.test(email.trim());
}

// Validar telefono
function validarTelefono() {
    const limpio = telefono.replace(/\s+/g, '').replace(/\+/g, '');
    return /^(56)?9\d{8}$/.test(limpio);
}

// Funcion para manipular el DOM y el feedback visual de bootstrap

function marcarCampo(inputElement, esValido, mensajeError="") {
    const feedback = inputElement.nextElementSibling;

    if (esValido) {
        inputElement.classList.remove('is-invalid');
        inputElement.classList.add('is-valid');
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.innerText = "";
        }
    } else {
        inputElement.classList.remove('is-valid');
        inputElement.classList.add('is-invalid');    
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.innerText = mensajeError;
        }
    }
    return esValido;
}

function validarFormularioCheckout() {
    let formularioValido = true;

    const nombre = document.getElementById('nombre');
    const rut = document.getElementById('rut');
    const email = document.getElementById('email');
    const telefono = document.getElementById('telefono');
    const direccion = document.getElementById('direccion');
    const region = document.getElementById('region');
    const comuna = document.getElementById('comuna');

    // Validacion nombre
    if (nombre.value.trim().lenght < 3) {
        marcarCampo(nombre, false, "Por favor, ingresa tu nombre completo.");
        formularioValido = false;
    } else {
        marcarCampo(nombre, true);
    }

    // Validacion rut
    if (!validarRut(rut.value)) {
        marcarCampo(rut, false, "El rut ingresado no es válido (ej: 12.345.678-9).");
        formularioValido = false;
    } else {
        marcarCampo(rut, true);
    }

    // Validacion email
    if (!validarEmail(email.value)) {
        marcarCampo(email, false, "Ingresa un correo electrónico válido.");
        formularioValido = false;
    } else {
        marcarCampo(email, true);
    }

    // Validacion telefono
    if (!validarTelefono(telefono.value)) {
        marcarCampo(telefono, false, "Ingresa un teléfono válido de 9 dígitos (ej: 9 1234 5678).");
        formularioValido = false;
    } else {
        marcarCampo(telefono, true);
    }

    // Validacion direccion
    if (direccion.value.trim().lenght < 5) {
        marcarCampo(direccion, false, "Ingresa una dirección completa (calle y número)");
        formularioValido = false;
    } else {
        marcarCampo(direccion, true);
    }

    // Validacion region
    if (!region.value) {
        marcarCampo(region, false, "Selecciona una región.");
        formularioValido = false;
    } else {
        marcarCampo(region, true);
    }

    // Validacion comuna
    if (comuna.value.trim().lenght < 3) {
        marcarCampo(comuna, false, "Ingresa tu comuna.");
        formularioValido = false;
    } else {
        marcarCampo(comuna, true);
    }

    return formularioValido;
}