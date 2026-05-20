<?php
session_start();
// Control de acceso y seguridad (RF-17) - Solo el administrador puede registrar usuarios
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Usuarios | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <script src="funciones/validarDatos.js"></script>
    <link rel="stylesheet" href="estilo/estilo.css">
</head>
<body>
    
    <div class="boton" style="margin: 20px;">
        <a href="menuprincipal.php" class="nuevo">Volver al Menú</a>
    </div>

    <div class="alta-contenedor">
        <form class="alta-form" name="form01" id="form01" method="post" action="usuarios_salva.php" onsubmit="return validarUsuario();">
            <h3>Alta de Nuevos Usuarios</h3>

            <input type="text" name="username" id="username" placeholder="Nombre de usuario (Login)" required><br>
            
            <input type="password" name="contraseña" id="contraseña" placeholder="Contraseña de acceso" required><br><br>

            <p><b>Rol del Usuario:</b> Empleado (Vendedor)</p>
<input type="hidden" name="rol" id="rol" value="empleado">
<br>
            </select><br><br>

            <input type="submit" value="Registrar Usuario" name="submit">
            <div id="form-error" style="color:red; margin-top:10px; text-align:center;"></div> 
        </form>
    </div> 
</body>
</html>