<?php
// =========================================================================
// Reportes.php - Página para generar reportes de ventas diarios y mensuales
// RF - 15 
// =========================================================================

// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'funciones/conexion.php'; 
$con = conecta();

// =========================================================================
// 2. RENDERIZADO DE INTERFAZ 
// =========================================================================
// Por defecto mostrará el reporte diario si no se especifica otro
$tipo_reporte = $_GET['tipo'] ?? 'diario'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes de Ventas - Mis Trapitos</title>
    <link rel="stylesheet" href="estilo/Estilo.css"> 
    <link rel="stylesheet" href="estilo/listas.css"> 
</head>
<body>

    <div class="menu-contenedor" style="width: 90%; margin: 0 auto;">
        
        <h1 class="titulo">Reportes de Ventas</h1>
        
        <!-- MENÚ DE NAVEGACIÓN LOCAL -->
        <div class="boton">
            <a href="GenerarReportes.php?tipo=diario" class="<?php echo $tipo_reporte === 'diario' ? 'nuevo' : 'accion'; ?>">Reporte Diario</a>
            <a href="GenerarReportes.php?tipo=mensual" class="<?php echo $tipo_reporte === 'mensual' ? 'nuevo' : 'accion'; ?>">Reporte Mensual</a>
            <a href="MenuPrincipal.php" class="accion eliminar">Volver al Menú</a>
        </div>

        <?php 
        // =================================================================
        // VISTA 1: REPORTE DIARIO
        // =================================================================
        if ($tipo_reporte === 'diario'): 
            // Usamos DATE(fecha) e incluimos conteo de clientes y metodos de pago
            $query = "SELECT DATE(fecha) as dia, 
                             COUNT(id_venta) as cantidad_ventas, 
                             COUNT(DISTINCT id_cliente) as clientes_atendidos,
                             STRING_AGG(DISTINCT metodo_pago, ', ') as metodos_usados,
                             SUM(total) as ingresos_totales 
                      FROM ventas 
                      GROUP BY DATE(fecha) 
                      ORDER BY dia DESC";
            
            $res = pg_query($con, $query);
        ?>
            <h3 style="color: var(--azul-principal); margin-bottom: 15px;">Resumen de Ventas por Día</h3>
            <table class="Opciones">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Ventas Realizadas</th>
                        <th>Clientes Registrados</th>
                        <th>Métodos de Pago Usados</th>
                        <th>Ingresos Totales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res && pg_num_rows($res) > 0): while ($fila = pg_fetch_assoc($res)): ?>
                    <tr>
                        <td><strong><?php echo date('d / m / Y', strtotime($fila['dia'])); ?></strong></td>
                        <td><?php echo $fila['cantidad_ventas']; ?></td>
                        <!-- Validamos si hubo clientes con ID o si compraron como "Público General" (NULL) -->
                        <td><?php echo ($fila['clientes_atendidos'] > 0) ? $fila['clientes_atendidos'] : 'Público en General'; ?></td>
                        <td><?php echo htmlspecialchars($fila['metodos_usados']); ?></td>
                        <td style="color: #2e7d32; font-weight: bold;">$<?php echo number_format($fila['ingresos_totales'], 2); ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align:center;">No hay registros de ventas diarios.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php 
        // =================================================================
        // VISTA 2: REPORTE MENSUAL
        // =================================================================
        elseif ($tipo_reporte === 'mensual'): 
            // Usamos TO_CHAR(fecha) para agrupar por mes y año
            $query = "SELECT TO_CHAR(fecha, 'YYYY-MM') as mes_anio, 
                             COUNT(id_venta) as cantidad_ventas, 
                             COUNT(DISTINCT id_cliente) as clientes_atendidos,
                             STRING_AGG(DISTINCT metodo_pago, ', ') as metodos_usados,
                             SUM(total) as ingresos_totales 
                      FROM ventas 
                      GROUP BY TO_CHAR(fecha, 'YYYY-MM') 
                      ORDER BY mes_anio DESC";
            
            $res = pg_query($con, $query);
        ?>
            <h3 style="color: var(--azul-principal); margin-bottom: 15px;">Resumen de Ventas por Mes</h3>
            <table class="Opciones">
                <thead>
                    <tr>
                        <th>Mes y Año</th>
                        <th>Ventas Realizadas</th>
                        <th>Clientes Registrados</th>
                        <th>Métodos de Pago Usados</th>
                        <th>Ingresos Totales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res && pg_num_rows($res) > 0): while ($fila = pg_fetch_assoc($res)): ?>
                    <tr>
                        <td><strong><?php echo $fila['mes_anio']; ?></strong></td>
                        <td><?php echo $fila['cantidad_ventas']; ?></td>
                        <td><?php echo ($fila['clientes_atendidos'] > 0) ? $fila['clientes_atendidos'] : 'Público en General'; ?></td>
                        <td><?php echo htmlspecialchars($fila['metodos_usados']); ?></td>
                        <td style="color: #2e7d32; font-weight: bold;">$<?php echo number_format($fila['ingresos_totales'], 2); ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align:center;">No hay registros de ventas mensuales.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div> 
</body>
</html>