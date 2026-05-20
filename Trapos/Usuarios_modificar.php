<?php
session_start();
// Control de acceso y seguridad (RF-17) - Solo el administrador puede modificar usuarios
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

include("funciones/conexion.php");
$con = conecta();

// Consultamos solo a los usuarios con rol de empleado para proteger la cuenta administrador
$query = "SELECT id_usuario, username, rol FROM usuarios WHERE rol = 'empleado' ORDER BY username ASC";
$resultado = pg_query($con, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Personal | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; padding-bottom: 40px; }
        .empleado-card-mod { background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s ease; border-left: 5px solid var(--azul-principal, #3498db); }
        .empleado-card-mod:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(52, 152, 219, 0.2); }

        .card-header-e { display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px; margin-bottom: 15px; }
        .avatar-e { width: 50px; height: 50px; border-radius: 50%; background: #e3f2fd; color: #1a5276; display: flex; justify-content: center; align-items: center; font-size: 24px; flex-shrink: 0; }
        
        .e-nombre { font-size: 16px; font-weight: 800; color: #1a5276; display: block; }
        .e-rol { font-size: 13px; color: #7f8c8d; font-weight: bold; text-transform: uppercase; }

        .datos-grid { display: grid; grid-template-columns: 1fr; gap: 10px; margin-bottom: 15px; }
        .dato-item { font-size: 14px; color: #555; display: flex; align-items: center; gap: 8px; }
        .dato-item i { color: var(--azul-principal, #3498db); font-size: 16px; }
        
        .action-button { background: var(--azul-principal, #3498db); color: white; border: none; padding: 12px 20px; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 15px; margin-top: 15px; display: block; text-align: center; transition: background 0.2s; }
        .action-button:hover { background: #1a5276; }
    </style>
</head>
<body>

    <div class="main-container" style="max-width: 95%; margin: 0 auto; padding: 20px;">

        <div class="dashboard-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
            <a href="menuprincipal.php" class="nuevo" style="text-decoration: none;"><i class="ri-arrow-left-line"></i> Menú</a>
            <div class="welcome-text" style="text-align: center; flex-grow: 1;">
                <h1><i class="ri-user-settings-line" style="color: var(--azul-principal, #3498db);"></i> Modificar Vendedores</h1>
                <p>Selecciona la cuenta del personal operativo que deseas actualizar</p>
            </div>
        </div>

        <div class="cards-grid">
            <?php
            if ($resultado && pg_num_rows($resultado) > 0) {
                while ($fila = pg_fetch_assoc($resultado)) { ?>
                    
                    <div class="empleado-card-mod">
                        
                        <div class="card-header-e">
                            <div class="avatar-e"> <i class="ri-user-3-line"></i> </div>
                            <div class="info-principal">
                                <span class="e-nombre"><?= htmlspecialchars($fila['username']) ?></span>
                                <span class="e-rol">Rol: <?= htmlspecialchars($fila['rol']) ?></span>
                            </div>
                        </div>

                        <div class="datos-grid">
                            <div class="dato-item">
                                <i class="ri-key-line"></i> <span>ID de Usuario Local: <b><?= htmlspecialchars($fila['id_usuario']) ?></b></span>
                            </div>
                            <div class="dato-item">
                                <i class="ri-shield-user-line"></i> Permisos: Acceso al módulo de ventas
                            </div>
                        </div>
                        
                        <a href="usuarios_editar.php?id=<?= $fila['id_usuario'] ?>" class="action-button">
                            <i class="ri-pencil-line"></i> Editar Cuenta
                        </a>
                    </div>

                <?php }
            } else { ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #888; background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);"> 
                    <p>No hay personal operativo (empleados) registrado en el sistema local.</p> 
                </div>
            <?php } pg_close($con); ?>
        </div>
    </div>
</body>
</html>