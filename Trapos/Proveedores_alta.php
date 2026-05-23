<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Proveedor | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="ImageL.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <a href="MenuPrincipal.php" class="nuevo" style="text-decoration: none; display: inline-block; margin: 20px;">
         <i class="ri-arrow-left-line"></i> Menu
    </a>

    <a href="proveedores_lista.php" class="nuevo" style="text-decoration: none; display: inline-block; margin: 20px;">
        <i class="ri-arrow-left-line"></i> Directorio
    </a>

    <div class="alta-contenedor">
        <form class="alta-form" method="post" action="proveedores_salva.php" autocomplete="off">
            
            <div class="logo-circulo" style="width: 50px; height: 50px; margin: 0 auto 15px auto; background: #e8f8f5; color: #117a65; display: flex; justify-content: center; align-items: center; border-radius: 50%;">
                <i class="ri-truck-line" style="font-size: 24px;"></i>
            </div>
            <h3 style="text-align: center; margin-bottom: 20px;">Registrar Nuevo Proveedor</h3>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Nombre de la Empresa o Contacto:</label><br>
                <i class="ri-building-line icono"></i>
                <input type="text" name="nombre" placeholder="Ej: Textiles Guadalajara S.A." required>
            </div>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Teléfono de Contacto:</label><br>
                <i class="ri-phone-line icono"></i>
                <input type="text" name="telefono" maxlength="20" placeholder="Ej: 3331224455">
            </div>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Correo Electrónico:</label><br>
                <i class="ri-mail-line icono"></i>
                <input type="email" name="correo" placeholder="Ej: proveedor@empresa.com">
            </div>

            <br>
            <input type="submit" value="Registrar Proveedor" name="submit">
        </form>
    </div>
</body>
</html>