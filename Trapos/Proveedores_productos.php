<?php
session_start();

// Control de acceso: Ambos roles pueden consultar el inventario y proveedores (RF-01, RF-09)
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

include("funciones/conexion.php");
$con = conecta();

// 1. Obtener el ID del proveedor seleccionado por la URL (GET)
$id_proveedor_sel = $_GET['id_proveedor'] ?? '';

// 2. Consultar todos los proveedores que NO estén eliminados para llenar el menú desplegable
$query_provs = "SELECT id_proveedor, nombre FROM proveedores WHERE eliminado = FALSE ORDER BY nombre ASC";
$res_provs = pg_query($con, $query_provs);

// 3. Si hay un proveedor seleccionado, consultar únicamente sus prendas asociadas
$resultado_productos = null;
if (!empty($id_proveedor_sel)) {
    $query_prod = "SELECT id_producto, nombre, precio, stock, categoria, talla, color 
                   FROM productos 
                   WHERE id_proveedor = $1
                   ORDER BY nombre ASC";
    $resultado_productos = pg_query_params($con, $query_prod, array($id_proveedor_sel));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos por Proveedor | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link rel="stylesheet" href="estilo/listas.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Selector de filtrado superior */
        .filtro-proveedor-box {
            background: #fdfefe;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 35px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .filtro-proveedor-box select {
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 300px;
            max-width: 100%;
        }

        /* Tonalidades de fondo de tabla oscurecidas con efecto cebra a petición */
        table {
            background-color: #ebedef; 
            border-radius: 8px;
            border-collapse: separate;
        }
        table th {
            background-color: #34495e;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #ebedef; /* Gris intermedio */
        }
        table tr:nth-child(odd) {
            background-color: #f4f6f7; /* Gris claro */
        }
        table tr:hover {
            background-color: #d6dbdf; /* Oscurece al pasar el cursor */
        }
    </style>
    <script>
        // Función JS para recargar la página inmediatamente al cambiar de proveedor
        function filtrarProveedor(id) {
            if(id) {
                window.location.href = "proveedores_productos.php?id_proveedor=" + id;
            } else {
                window.location.href = "proveedores_productos.php";
            }
        }
    </script>
</head>
<body>
    <div class="menu-contenedor" style="max-width: 95%; margin: 20px auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <a href="menu_principal.php" class="nuevo" style="text-decoration: none;"><i class="ri-arrow-left-line"></i> Menú</a>
            <h1 style="margin:0;"><i class="ri-folder-shield-2-line" style="color: #27ae60;"></i> Catálogo por Proveedor</h1>
            <div></div>
        </div>

        <div class="filtro-proveedor-box">
            <label style="font-weight: bold; color: #34495e; margin-right: 10px; font-size: 15px;">Selecciona un Proveedor:</label>
            <select onchange="filtrarProveedor(this.value)">
                <option value="">-- Elige una opción --</option>
                <?php 
                while ($p = pg_fetch_assoc($res_provs)) {
                    $selected = ($p['id_proveedor'] == $id_proveedor_sel) ? 'selected' : '';
                    echo "<option value='{$p['id_proveedor']}' {$selected}>" . htmlspecialchars($p['nombre']) . "</option>";
                }
                ?>
            </select>
        </div>

        <?php if (!empty($id_proveedor_sel)): ?>
            <table>
                <tr>
                    <th>ID Producto</th>
                    <th>Descripción de la Prenda</th>
                    <th>Categoría</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Precio Unitario</th>
                    <th>Stock Disponible</th>
                </tr>

                <?php
                if ($resultado_productos && pg_num_rows($resultado_productos) > 0) {
                    while ($fila = pg_fetch_assoc($resultado_productos)) { 
                        $claseStock = ($fila['stock'] <= 0) ? "style='color: red; font-weight: bold;'" : "";
                        ?>
                        <tr>
                            <td><b><?= htmlspecialchars($fila['id_producto']) ?></b></td>
                            <td><?= htmlspecialchars($fila['nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['categoria']) ?></td>
                            <td><span class="status-badge" style="background:#eaecee; color:#2c3e50; padding: 2px 6px; font-size:12px;"><?= htmlspecialchars($fila['talla']) ?></span></td>
                            <td><?= htmlspecialchars($fila['color']) ?></td>
                            <td style="font-weight: bold;">$<?= number_format($fila['precio'], 2) ?></td>
                            <td <?= $claseStock ?>><?= htmlspecialchars($fila['stock']) ?> pzas</td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #7f8c8d; background-color: #f4f6f7;">
                            Este proveedor aún no tiene prendas o productos registrados en el almacén local.
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: #f8f9f9; border-radius: 8px; color: #7f8c8d; border: 1px dashed #ccc;">
                <i class="ri-search-eye-line" style="font-size: 40px; color: #bdc3c7; display: block; margin-bottom: 10px;"></i>
                Por favor, selecciona una empresa o distribuidor en el menú de arriba para consultar sus prendas.
            </div>
        <?php endif; pg_close($con); ?>
        
    </div>
</body>
</html>