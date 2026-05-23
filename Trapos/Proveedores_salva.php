<?php
session_start();
require "funciones/conexion.php";
$con = conecta();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

$nombre   = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo   = trim($_POST['correo'] ?? '');

if (empty($nombre)) {
    echo "<script>alert('El nombre del proveedor es obligatorio.'); window.history.back();</script>";
    exit();
}

$sql = "INSERT INTO proveedores (nombre, telefono, correo) VALUES ($1, $2, $3)";
$result = pg_query_params($con, $sql, array($nombre, $telefono, $correo));

if ($result) {
    echo "<script>alert('Proveedor registrado con éxito.'); window.location.href='proveedores_lista.php';</script>";
} else {
    $error = pg_last_error($con);
    echo "<script>alert('Error al registrar: " . addslashes($error) . "'); window.history.back();</script>";
}

pg_close($con);
?>