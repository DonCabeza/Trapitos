<?php
session_start();
require "funciones/conexion.php";
$con = conecta();

// 1. Control de acceso y seguridad (RF-17)
// Solo el administrador original puede ejecutar este script de guardado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

// 2. Captura y limpieza de datos provenientes del formulario usuarios_alta.php
$username = trim($_POST['username'] ?? '');
$contraseña = trim($_POST['contraseña'] ?? '');

// 3. CLÁUSULA DE SEGURIDAD ABSOLUTA
// Forzamos que el rol sea única y estrictamente 'empleado' por código de servidor
$rol = 'empleado'; 

// 4. Validación de campos obligatorios en el servidor
if (empty($username) || empty($contraseña)) {
    echo "<script>alert('Faltan campos por llenar.'); window.history.back();</script>";
    exit();
}

// 5. Preparación de la consulta SQL para tu tabla unificada en PostgreSQL
// Recuerda que 'password_hash' ahora guardará la contraseña normal en texto plano para tus pruebas
$sql = 'INSERT INTO usuarios (username, password_hash, rol) VALUES ($1, $2, $3)';

$result = pg_query_params($con, $sql, array(
    $username, 
    $contraseña, 
    $rol
));

// 6. Verificación del resultado de la inserción
if ($result) {
    // Si la base de datos local lo acepta, avisa y limpia la pantalla regresando al alta
    echo "<script>alert('Empleado registrado correctamente en el sistema.'); window.location.href='usuarios_alta.php';</script>";
} else {
    // Si el 'username' ya existe (violación de restricción UNIQUE), PostgreSQL saltará aquí
    $error = pg_last_error($con);
    echo "<script>alert('Error al registrar el empleado: " . addslashes($error) . "'); window.history.back();</script>";
}

pg_close($con);
?>