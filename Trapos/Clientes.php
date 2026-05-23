<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'funciones/conexion.php'; 
$con = conecta();

// =========================================================================
// 2. LÓGICA DE ENVÍO DE DATOS (Solo se ejecuta al Guardar)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Lógica para registrar un nuevo cliente
    if ($accion === 'registrar') {
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);

        $query = 'INSERT INTO cliente (nombre, telefono) VALUES ($1, $2)';
        $parametros = array($nombre, $telefono);
        
        $resultado = pg_query_params($con, $query, $parametros);
        
        if ($resultado) {
            header("Location: Clientes.php?seccion=ver&msg=exito");
        } else {
            $error_db = pg_last_error($con);
            header("Location: Clientes.php?seccion=registrar&msg=" . urlencode($error_db));
        }
        exit(); 
    }
}

// =========================================================================
// 3. RENDERIZADO DE INTERFAZ 
// =========================================================================
$seccion = $_GET['seccion'] ?? 'ver';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo de Clientes - Mis Trapitos</title>
    <link rel="stylesheet" href="estilo/Estilo.css"> 
    <link rel="stylesheet" href="estilo/listas.css"> 
</head>
<body>

    <div class="menu-contenedor" style="width: 85%; margin: 0 auto;">
        
        <h1 class="titulo">Gestión de Clientes</h1>
        
        <!-- MENÚ DE NAVEGACIÓN LOCAL CENTRADO -->
        <div class="boton" style="display: flex; justify-content: center; gap: 15px; margin-bottom: 25px;">
            <a href="Clientes.php?seccion=ver" class="<?php echo $seccion === 'ver' ? 'nuevo' : 'accion'; ?>">Ver Clientes y Compras</a>
            <a href="Clientes.php?seccion=registrar" class="<?php echo $seccion === 'registrar' ? 'nuevo' : 'accion'; ?>">Registrar Cliente</a>
            <a href="MenuPrincipal.php" class="accion eliminar">Volver al Menú</a>
        </div>

        <!-- MANEJO DE ALERTAS -->
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'exito'): ?>
                <div class="mensaje exito">¡Operación realizada correctamente!</div>
            <?php else: ?>
                <div class="mensaje error">
                    <strong>Error en la base de datos:</strong> <br>
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php 
        // =================================================================
        // SUB-RENDERIZADO: VER HISTORIAL DE CLIENTES
        // =================================================================
        if ($seccion === 'ver'): 
            // Hacemos una unión (JOIN) con la tabla ventas para sacar su historial matemático
            $query = "SELECT c.id_cliente, 
                             c.nombre, 
                             c.telefono, 
                             COUNT(v.id_venta) as total_compras, 
                             COALESCE(SUM(v.total), 0) as dinero_gastado 
                      FROM cliente c 
                      LEFT JOIN ventas v ON c.id_cliente = v.id_cliente 
                      GROUP BY c.id_cliente, c.nombre, c.telefono 
                      ORDER BY total_compras DESC, c.id_cliente DESC";
                      
            $res = pg_query($con, $query);
        ?>
            <h3 style="color: var(--azul-principal); margin-bottom: 15px;">Directorio y Compras Acumuladas</h3>
            <table class="Opciones">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Cliente</th>
                        <th>Teléfono</th>
                        <th>Compras Realizadas</th>
                        <th>Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res && pg_num_rows($res) > 0): while ($fila = pg_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $fila['id_cliente']; ?></td>
                        <td><strong><?php echo htmlspecialchars($fila['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                        <td>
                            <?php if($fila['total_compras'] > 0): ?>
                                <span style="background-color: #e3f2fd; color: #1565c0; padding: 4px 8px; border-radius: 4px; font-weight: bold;">
                                    <?php echo $fila['total_compras']; ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #999;">Sin compras</span>
                            <?php endif; ?>
                        </td>
                        <td style="color: #2e7d32; font-weight: bold;">
                            $<?php echo number_format($fila['dinero_gastado'], 2); ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align:center;">Aún no tienes clientes registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php 
        // =================================================================
        // SUB-RENDERIZADO: FORMULARIO DE REGISTRO
        // =================================================================
        elseif ($seccion === 'registrar'): 
        ?>
            <div class="alta-form" style="margin: 0 auto; width: 50%;">
                <h3>Dar de Alta a un Cliente</h3>
                <form action="Clientes.php" method="POST">
                    <input type="hidden" name="accion" value="registrar">
                    
                    <label style="display:block; text-align:left; font-size:14px; color:var(--azul-principal);">Nombre Completo:</label>
                    <input type="text" name="nombre" placeholder="Ej. María Fernanda López" required>
                    
                    <label style="display:block; text-align:left; font-size:14px; color:var(--azul-principal); margin-top: 10px;">Teléfono:</label>
                    <input type="tel" name="telefono" placeholder="Ej. 33 1234 5678" maxlength="20" required>
                    
                    <input type="submit" value="Guardar Cliente" class="nuevo" style="width: 100%; margin-top: 20px;">
                </form>
            </div>
        <?php endif; ?>

    </div> 
</body>
</html>