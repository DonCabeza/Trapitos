<?php
session_start();

// Control de acceso: Ambos roles (administrador y empleado) pueden consultar el inventario (RF-01)
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

include("funciones/conexion.php");
$con = conecta();

// Consultamos todos los campos necesarios de la tabla de PRODUCTOS
$query = "SELECT id_producto, nombre, precio, stock, categoria, talla, color 
          FROM productos 
          ORDER BY id_producto ASC";

$resultado = pg_query($con, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Almacén | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link rel="stylesheet" href="estilo/listas.css">
</head>
<body>
    <div class="menu-contenedor" style="max-width: 95%; margin: 20px auto;">
        <h1>Inventario de Prendas Registradas</h1>

        <table>
            <tr>
                <th>ID Producto</th>
                <th>Descripción / Nombre</th>
                <th>Precio</th>
                <th>Stock (Existencia)</th>
                <th>Categoría</th>
                <th>Talla</th>
                <th>Color</th>
            </tr>

            <?php
            if ($resultado && pg_num_rows($resultado) > 0) {
                while ($fila = pg_fetch_assoc($resultado)) {
                    // Validamos visualmente si un producto se quedó sin existencias
                    $claseStock = ($fila['stock'] <= 0) ? "style='color: red; font-weight: bold;'" : "";
                    
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($fila['id_producto']) . "</td>";
                    echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                    echo "<td>$" . number_format($fila['precio'], 2) . "</td>";
                    echo "<td {$claseStock}>" . htmlspecialchars($fila['stock']) . " pzas</td>";
                    echo "<td>" . htmlspecialchars($fila['categoria']) . "</td>";
                    echo "<td>" . htmlspecialchars($fila['talla']) . "</td>";
                    echo "<td>" . htmlspecialchars($fila['color']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No hay productos registrados en el almacén actualmente.</td></tr>";
            }
            pg_close($con);
            ?>
        </table>

        <br>
        <a href="menuprincipal.php" class="nuevo">Volver al menú</a>
    </div>
</body>
</html>