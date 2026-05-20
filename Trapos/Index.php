<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <script src="funciones/ValidarDatos.js"></script>
    <link rel="stylesheet" href="estilo/estilo.css">
</head>
<body>
    <div class="login-contenedor">
        <form class="login-form" id="loginForm" onsubmit="validarLog(); return false;">
            <h2>Mis Trapitos</h2>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">Sistema de Gestión Local</p>
            
            <input type="text" id="usuario" name="usuario" placeholder=" Usuario" required><br>
            <input type="password" id="contraseña" name="contraseña" placeholder=" Contraseña" required><br><br>
            
            <input type="submit" value="Entrar">
            <div id="mensaje" style="color:red; margin-top:10px; text-align:center;"></div>
        </form>
    </div> 
</body>
</html>