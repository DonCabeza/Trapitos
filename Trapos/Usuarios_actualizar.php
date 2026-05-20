<?php
session_start();
require "funciones/conexion.php";
$con = conecta();

// 1. Control de acceso y seguridad (RF-17)
// Solo el administrador puede procesar la actualización de usuarios
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

// 2. Recibir los datos desde el formulario de usuarios_edita.php
$id = $_POST['id_usuario'] ?? '';
$username = trim($_POST['username'] ?? '');
$contraseña = trim($_POST['contraseña'] ?? '');

// 3. Validar que los campos obligatorios no estén vacíos
if (empty($id) || empty($username)) {
    echo "<script>alert('Faltan campos obligatorios.'); window.history.back();</script>";
    exit();
}

// 4. Determinar la lógica de la contraseña
if (!empty($contraseña)) {

    $sql = "UPDATE usuarios 
            SET username = $1, 
                password_hash = $2 
            WHERE id_usuario = $3 AND rol = 'empleado'";
    $parametros = array($username, $contraseña, $id);
} else {
    
    $sql = "UPDATE usuarios 
            SET username = $1 
            WHERE id_usuario = $2 AND rol = 'empleado'";
    $parametros = array($username, $id);
}

// 5. Ejecutar la consulta con parámetros seguros en PostgreSQL
$result = pg_query_params($con, $sql, $parametros);

if ($result) {
    
    echo "<script>alert('Empleado actualizado correctamente.'); window.location.href='usuarios_modificar.php';</script>";
} else {
    
    $error = pg_last_error($con);
    echo "<script>alert('Error al actualizar en la base de datos: " . addslashes($error) . "'); window.history.back();</script>";
}

pg_close($con);
?>
