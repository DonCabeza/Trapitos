<?php
session_start();

// Control de acceso estricto: Solo el administrador puede crear promociones
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

require "funciones/conexion.php";
$con = conecta();

// Consultamos los productos disponibles para poblar el selector del formulario
$query_productos = "SELECT id_producto, nombre, talla, color, precio FROM productos ORDER BY nombre ASC";
$res_productos = pg_query($con, $query_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Promoción | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

    <a href="MenuPrincipal.php" class="nuevo" style="text-decoration: none; display: inline-block; margin: 20px;">
        <i class="ri-arrow-left-line"></i> Cancelar / Volver
    </a>

    <div class="alta-contenedor">
        
        <form class="alta-form" method="post" action="promociones_salva.php" autocomplete="off">
            
            <div class="logo-circulo" style="width: 50px; height: 50px; margin: 0 auto 15px auto; background: #ffe0b2; color: #fb8c00; display: flex; justify-content: center; align-items: center; border-radius: 50%;">
                <i class="ri-price-tag-3-line" style="font-size: 24px;"></i>
            </div>
            <h3 style="text-align: center; margin-bottom: 20px;">Nueva Promoción Especial</h3>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Nombre de la Campaña / Promoción:</label><br>
                <i class="ri-advertisement-line icono"></i>
                <input type="text" name="nombre_promo" placeholder="Ej: Rebajas de Verano, Descuento Buen Fin" required>
            </div>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Seleccionar Prenda Exclusiva:</label><br>
                <i class="ri-shirt-line icono"></i>
                <select name="id_producto" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; text-indent: 25px;">
                    <option value="" disabled selected>-- Selecciona un artículo --</option>
                    <?php 
                    while ($row = pg_fetch_assoc($res_productos)) {
                        echo "<option value='{$row['id_producto']}'>" . htmlspecialchars($row['nombre']) . " ({$row['talla']} - " . htmlspecialchars($row['color']) . ") - \${$row['precio']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="input-box" style="margin-bottom: 15px;">
                <label style="font-size: 14px; font-weight: bold; color: #555;">Porcentaje de Rebaja (%):</label><br>
                <i class="ri-percent-line icono"></i>
                <input type="number" name="porcentaje" step="0.01" min="1" max="100" placeholder="Ej: 15.50" required>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div class="input-box" style="flex: 1;">
                    <label style="font-size: 14px; font-weight: bold; color: #555;">Fecha Inicio:</label><br>
                    <i class="ri-calendar-todo-line icono"></i>
                    <input type="date" name="fecha_inicio" value="<?= date('Y-m-d'); ?>" required style="width:100%;">
                </div>
                
                <div class="input-box" style="flex: 1;">
                    <label style="font-size: 14px; font-weight: bold; color: #555;">Fecha Fin:</label><br>
                    <i class="ri-calendar-line icono"></i>
                    <input type="date" name="fecha_fin" required style="width:100%;">
                </div>
            </div>

            <br>
            <input type="submit" value="Activar Descuento" name="submit">
        </form>
    </div>

</body>
</html>
<?php pg_close($con); ?>