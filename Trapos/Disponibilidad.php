<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'funciones/conexion.php'; 
$con = conecta();

// =========================================================================
// 2. CONSULTA DE DISPONIBILIDAD (STOCK)
// =========================================================================
// Usamos LEFT JOIN para que los productos sin proveedor también aparezcan
$query = "SELECT p.nombre, 
                 p.categoria, 
                 p.stock, 
                 COALESCE(pr.nombre, 'Sin proveedor') as proveedor_nombre 
          FROM productos p 
          LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor 
          ORDER BY p.stock ASC"; // Ordenamos por stock para ver qué se está agotando primero

$res = pg_query($con, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Disponibilidad de Inventario - Mis Trapitos</title>
    <link rel="stylesheet" href="estilo/Estilo.css"> 
    <link rel="stylesheet" href="estilo/listas.css"> 
</head>
<body>

    <div class="menu-contenedor" style="width: 80%; margin: 0 auto;">
        
        <h1 class="titulo">Disponibilidad de Artículos</h1>
        
        <!-- MENÚ DE NAVEGACIÓN LOCAL -->
        <div class="boton" style="display: flex; justify-content: center; margin-bottom: 25px;">
            <a href="MenuPrincipal.php" class="accion eliminar">Volver al Menú Principal</a>
        </div>

        <h3 style="color: var(--azul-principal); margin-bottom: 15px;">Reporte de Stock Actual</h3>
        
        <table class="Opciones">
            <thead>
                <tr>
                    <th>Nombre del Producto</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Stock Disponible</th>
                </tr>
            </thead>
            <tbody>
                <?php if($res && pg_num_rows($res) > 0): while ($fila = pg_fetch_assoc($res)): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($fila['nombre']); ?></strong></td>
                    <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($fila['proveedor_nombre']); ?></td>
                    <td>
                        <?php 
                        // Pequeña lógica visual para resaltar si el stock es bajo
                        if ($fila['stock'] <= 5) {
                            echo '<span style="color: #d32f2f; font-weight: bold;">' . $fila['stock'] . ' (¡Bajo!)</span>';
                        } else {
                            echo '<span style="color: #2e7d32; font-weight: bold;">' . $fila['stock'] . '</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="4" style="text-align:center;">No hay productos registrados en el inventario.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div> 
</body>
</html>