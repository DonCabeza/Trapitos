<?php
session_start();
// Control de acceso (Solo el administrador puede editar usuarios)
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

require "funciones/conexion.php";
$con = conecta();


if (!isset($_GET['id'])) {
    header("Location: usuarios_modificar.php");
    exit();
}

$id = $_GET['id'];


$sql = "SELECT id_usuario, username FROM usuarios WHERE id_usuario = $1 AND rol = 'empleado'";
$res = pg_query_params($con, $sql, array($id));

if (pg_num_rows($res) === 0) {
    echo "<script>alert('Empleado no encontrado'); window.location.href='usuarios_modificar.php';</script>";
    exit();
}

$fila = pg_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Vendedor | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <script src="funciones/validarDatos.js"></script>
    <link rel="stylesheet" href="estilo/estilo.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

    <a href="usuarios_modificar.php" class="nuevo" style="text-decoration: none; display: inline-block; margin: 20px;">
        <i class="ri-arrow-left-line"></i> Cancelar
    </a>

    <div class="alta-contenedor">
        
        <form class="alta-form" method="post" action="usuarios_actualizar.php" autocomplete="off">
            
            <input type="hidden" name="id_usuario" value="<?= $fila['id_usuario'] ?>">

            <div class="logo-circulo" style="width: 50px; height: 50px; margin: 0 auto 15px auto; background: #e3f2fd; color: #1a5276; display: flex; justify-content: center; align-items: center; border-radius: 50%;">
                <i class="ri-user-settings-line" style="font-size: 24px;"></i>
            </div>
            <h3 style="text-align: center; margin-bottom: 20px;">Editar Datos de Cuenta</h3>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Nombre de Usuario (Login):</label><br>
                <i class="ri-user-line icono"></i>
                <input type="text" name="username" value="<?= htmlspecialchars($fila['username']) ?>" required>
            </div>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Nueva Contraseña:</label><br>
                <i class="ri-key-line icono"></i>
                <input type="password" name="contraseña" placeholder="Dejar en blanco para mantener la actual">
                <small style="color: #7f8c8d; display: block; margin-top: 5px;">*Si no deseas cambiar la contraseña del vendedor, no escribas nada aquí.</small>
            </div>

            <br>
            <input type="submit" value="Guardar Cambios" name="submit">
        </form>
    </div>

</body>
</html>
