<?php
session_start();
require "funciones/conexion.php";
$con = conecta();

// Validación de seguridad de rol en el backend
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

// Recibir datos del formulario
$nombre_promo = trim($_POST['nombre_promo'] ?? '');
$id_producto  = $_POST['id_producto'] ?? '';
$porcentaje   = $_POST['porcentaje'] ?? '';
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin    = $_POST['fecha_fin'] ?? '';

// 1. Validar campos obligatorios
if (empty($nombre_promo) || empty($id_producto) || empty($porcentaje) || empty($fecha_inicio) || empty($fecha_fin)) {
    echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
    exit();
}

// 2. Validar consistencia de fechas (Que no termine antes de empezar)
if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
    echo "<script>alert('Error: La fecha de finalización no puede ser menor a la fecha de inicio.'); window.history.back();</script>";
    exit();
}

// 3. Preparar la inserción con los nuevos campos optimizados
$sql = "INSERT INTO public.descuentos (porcentaje, id_producto, nombre_promo, fecha_inicio, fecha_fin, activo) 
        VALUES ($1, $2, $3, $4, $5, TRUE)";

$result = pg_query_params($con, $sql, array(
    $porcentaje, 
    $id_producto, 
    $nombre_promo, 
    $fecha_inicio, 
    $fecha_fin
));

if ($result) {
    echo "<script>alert('Promoción registrada y aplicada al inventario correctamente.'); window.location.href='MenuPrincipal.php';</script>";
} else {
    $error = pg_last_error($con);
    echo "<script>alert('Error al guardar en la base de datos local: " . addslashes($error) . "'); window.history.back();</script>";
}

pg_close($con);
?>