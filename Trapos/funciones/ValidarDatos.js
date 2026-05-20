
function validarLog() {
    // 1. Obtener los valores de los campos del formulario index.php
    var usuario = document.getElementById("usuario").value.trim();
    var contrasena = document.getElementById("contraseña").value.trim();
    var mensajeDiv = document.getElementById("mensaje");

    // Limpiar mensajes de error previos
    if (mensajeDiv) {
        mensajeDiv.textContent = "";
    }

    // 2. Crear los parámetros que se enviarán por POST a valida.php
    var datos = new URLSearchParams();
    datos.append('usuario', usuario);
    datos.append('contraseña', contrasena);

    // 3. Enviar la solicitud asíncrona (AJAX) mediante Fetch API
    fetch('funciones/Valida.php', {
        method: 'POST',
        body: datos
    })
    .then(response => response.text()) 
    .then(texto => {
        if (texto.trim() === "OK") {
            // Caso Exitoso: Redirección inmediata al archivo del menú principal
            window.location.href = "MenuPrincipal.php"; 
        } else {
            // Caso de Error: Desplegar el mensaje regresado por valida.php ("Usuario o contraseña incorrectos")
            if (mensajeDiv) {
                mensajeDiv.textContent = texto;
            }
        }
    })
    .catch(error => {
        console.error('Error en la conexión local:', error);
        if (mensajeDiv) {
            mensajeDiv.textContent = "Error de conexión con el servidor local.";
        }
    });
}
// 1. Validar Alta/Modificación de Usuarios - RF-17
function validarUsuario() {
    var username = document.getElementById("username").value.trim();
    var contrasena = document.getElementById("contraseña").value.trim();
    var rol = document.getElementById("rol").value.trim();
    var errorDiv = document.getElementById("form-error");

    if (username === "" || contrasena === "" || rol === "") {
        if (errorDiv) {
            errorDiv.textContent = "Faltan campos obligatorios para el usuario.";
            setTimeout(() => errorDiv.textContent = "", 5000);
        }
        return false;
    }

    if (rol !== "administrador" && rol !== "empleado") {
        if (errorDiv) errorDiv.textContent = "El rol seleccionado no es válido.";
        return false;
    }

    return true;
}

// 2. Validar Alta de Clientes - RF-07
function validarCliente() {
    var nombre = document.getElementById("nombre").value.trim();
    var telefono = document.getElementById("telefono").value.trim();
    var errorDiv = document.getElementById("form-error");

    if (nombre === "" || telefono === "") {
        if (errorDiv) {
            errorDiv.textContent = "Por favor, complete todos los campos del cliente.";
            setTimeout(() => errorDiv.textContent = "", 5000);
        }
        return false;
    }

    if (isNaN(telefono) || telefono.length < 10) {
        if (errorDiv) errorDiv.textContent = "El teléfono debe contener al menos 10 dígitos numéricos.";
        return false;
    }

    return true;
}

// 3. Validar Alta de Proveedores  - RF-09
function validarProveedor() {
    let nombre = document.getElementById("nombre").value.trim();
    let telefono = document.getElementById("telefono").value.trim();
    let correo = document.getElementById("correo").value.trim();
    let errorDiv = document.getElementById("form-error");

    if (nombre === "" || telefono === "" || correo === "") {
        if (errorDiv) errorDiv.textContent = "Por favor, completa los datos de contacto del proveedor.";
        return false;
    }

    if (isNaN(telefono)) {
        if (errorDiv) errorDiv.textContent = "El teléfono del proveedor debe contener solo números.";
        return false;
    }

    // Validación básica de correo electrónico
    if (!correo.includes("@") || !correo.includes(".")) {
        if (errorDiv) errorDiv.textContent = "Ingrese un correo electrónico válido.";
        return false;
    }

    return true;
}

// 4. Validar Registro de Productos en Almacén  - RF-01, RF-05
function validarProducto() {
    let nombre = document.getElementById("nombre").value.trim();
    let precio = document.getElementById("precio").value.trim();
    let stock = document.getElementById("stock").value.trim();
    let categoria = document.getElementById("categoria").value.trim();
    let errorDiv = document.getElementById("form-error");

    if (nombre === "" || precio === "" || stock === "" || categoria === "") {
         if (errorDiv) errorDiv.textContent = "Faltan características obligatorias del producto.";
         return false;
    }

    if (isNaN(precio) || parseFloat(precio) <= 0) {
        if (errorDiv) errorDiv.textContent = "El precio debe ser un número mayor a 0.";
        return false;
    }

    if (isNaN(stock) || parseInt(stock) < 0) {
        if (errorDiv) errorDiv.textContent = "El stock disponible no puede ser un número negativo.";
        return false;
    }

    return true;
}
