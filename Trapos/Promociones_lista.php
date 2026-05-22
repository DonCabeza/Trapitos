<?php
session_start();

// Control de acceso: Ambos roles pueden ver las promociones vigentes
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

include("funciones/conexion.php");
$con = conecta();

// 1. Detectar el filtro inicial que viene por la URL (GET)
// Valores posibles: 'todas', 'activas', 'inactivas'
$filtro = $_GET['filtro'] ?? 'todas';

// 2. Construir la consulta SQL dinámica con base en el filtro y la vigencia
$query = "SELECT d.id_descuento, d.nombre_promo, d.porcentaje, d.fecha_inicio, d.fecha_fin, d.activo,
                 p.nombre AS producto_nombre, p.talla, p.color, p.precio AS precio_original
          FROM descuentos d
          INNER JOIN productos p ON d.id_producto = p.id_producto";

if ($filtro === 'activas') {
    // Filtra las que el switch está en TRUE Y que el día de hoy esté en su rango de calendario
    $query .= " WHERE d.activo = TRUE AND CURRENT_DATE BETWEEN d.fecha_inicio AND d.fecha_fin";
} elseif ($filtro === 'inactivas') {
    // Filtra las que fueron apagadas manualmente O cuyo calendario ya caducó
    $query .= " WHERE d.activo = FALSE OR CURRENT_DATE > d.fecha_fin";
}

$query .= " ORDER BY d.fecha_fin DESC";
$resultado = pg_query($con, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promociones y Descuentos | Mis Trapitos</title>
     <link rel="icon" type="image/png" href="ImageL.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link rel="stylesheet" href="estilo/listas.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
  
</head>
<body>
    <div class="menu-contenedor" style="max-width: 95%; margin: 20px auto;">
        
        <div style="text-align: center; margin-bottom: 25px;">
            <h1><i class="ri-price-tag-3-line" style="color: #fb8c00;"></i> Control de Promociones</h1>
            <p>Monitoreo de ofertas y rebajas del catálogo de prendas</p>
        </div>

        <div class="filtros-contenedor">
            <a href="promociones_lista.php?filtro=todas" class="btn-filtro <?= $filtro === 'todas' ? 'activo' : '' ?>">
                <i class="ri-list-check"></i> Ver Todas
            </a>
            <a href="promociones_lista.php?filtro=activas" class="btn-filtro <?= $filtro === 'activas' ? 'activo' : '' ?>">
                <i class="ri-checkbox-circle-line"></i> Solo Activas Vigorosas
            </a>
            <a href="promociones_lista.php?filtro=inactivas" class="btn-filtro <?= $filtro === 'inactivas' ? 'activo' : '' ?>">
                <i class="ri-close-circle-line"></i> Solo Inactivas / Vencidas
            </a>
        </div>

        <table>
            <tr>
                <th>Campaña</th>
                <th>Prenda / Artículo</th>
                <th>Precio Reg.</th>
                <th>Descuento</th>
                <th>Precio Oferta</th>
                <th>Vigencia (Calendario)</th>
                <th>Estado Actual</th>
            </tr>

            <?php
            if ($resultado && pg_num_rows($resultado) > 0) {
                while ($fila = pg_fetch_assoc($resultado)) {
                    // Validar matemáticamente si en este instante de la renderización la promo es válida
                    $hoy = strtotime(date('Y-m-d'));
                    $inicio = strtotime($fila['fecha_inicio']);
                    $fin = strtotime($fila['fecha_fin']);
                    $es_activa = ($fila['activo'] === 't' && $hoy >= $inicio && $hoy <= $fin);

                    $precio_original = floatval($fila['precio_original']);
                    $porcentaje = floatval($fila['porcentaje']);
                    $precio_oferta = $precio_original - ($precio_original * ($porcentaje / 100));
                    ?>
                    
                    <tr>
                        <td><b><?= htmlspecialchars($fila['nombre_promo']) ?></b></td>
                        <td><?= htmlspecialchars($fila['producto_nombre']) ?> <small style="color:#7f8c8d;">(<?= htmlspecialchars($fila['talla']) ?> - <?= htmlspecialchars($fila['color']) ?>)</small></td>
                        <td>$<?= number_format($precio_original, 2) ?></td>
                        <td style="color: #e67e22; font-weight: bold;"><?= $porcentaje ?>%</td>
                        <td style="color: #2e7d32; font-weight: bold;">$<?= number_format($precio_oferta, 2) ?></td>
                        <td>
                            <small>
                                <i class="ri-calendar-event-line"></i> Del: <?= date('d/m/Y', $inicio) ?><br>
                                <i class="ri-calendar-check-line"></i> Al: <?= date('d/m/Y', $fin) ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($es_activa): ?>
                                <span class="status-badge activa"><i class="ri-check-line"></i> Activa</span>
                            <?php else: ?>
                                <span class="status-badge inactiva"><i class="ri-time-line"></i> Inactiva</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                <?php }
            } else { ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #888;">
                        No se encontraron promociones bajo el filtro seleccionado.
                    </td>
                </tr>
            <?php } pg_close($con); ?>
        </table>

        <br>
        <a href="MenuPrincipal.php" class="nuevo">Volver al menú</a>
    </div>
</body>
</html>