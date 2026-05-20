<?php
session_start();
require "conexion.php";
$con = conecta();

// Captura y limpieza de datos del formulario (RF-17)
$usuario = trim($_POST['usuario'] ?? '');
$pass = trim($_POST['contraseña'] ?? '');

if (empty($usuario) || empty($pass)) {
    echo "Por favor, llene todos los campos.";
    exit;
}

// Consultamos al usuario en la base de datos local de PostgreSQL
$query = 'SELECT id_usuario, username, password_hash, rol FROM usuarios WHERE username = $1';
$result = pg_query_params($con, $query, array($usuario));

if ($result && pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);
    
    // ====================================================================
    // CAMBIO CLAVE: Comparamos texto plano directamente usando ===
    // ====================================================================
    if ($pass === trim($user['password_hash'])) {
        
        // Configuración de variables de sesión globales del proyecto
        $_SESSION['rol']        = trim($user['rol']); 
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['usuario']    = $user['username'];
        
        // Respondemos OK para que el script JS ejecute la redirección al menú
        echo "OK";
        pg_close($con);
        exit;
    }
}

// Si el usuario no existe o la contraseña de texto plano no coincide
echo "Usuario o contraseña incorrectos";

pg_close($con);
?>