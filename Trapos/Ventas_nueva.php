<?php
session_start();
// Control de acceso: Tanto administrador como empleado pueden vender
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

include("funciones/conexion.php");
$con = conecta();

// 1. Obtener los clientes para el selector
$query_clientes = "SELECT id_cliente, nombre FROM cliente ORDER BY nombre ASC";
$res_clientes = pg_query($con, $query_clientes);

// 2. Obtener los productos con stock disponible para el selector del carrito
$query_productos = "SELECT id_producto, nombre, precio, stock, talla, color FROM productos WHERE stock > 0 ORDER BY nombre ASC";
$res_productos = pg_query($con, $query_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Venta | Mis Trapitos</title>
    <script src="Funciones/VentasFunciones.js"></script>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .ventas-grid { display: grid; grid-template-columns: 1fr 350px; gap: 20px; margin-top: 20px; }
        .panel-izquierdo, .panel-derecho { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .selector-producto { display: flex; gap: 10px; margin-bottom: 20px; align-items: flex-end; }
        .selector-producto div { flex-grow: 1; }
        .tabla-carrito { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla-carrito th, .tabla-carrito td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .btn-eliminar { color: #e74c3c; cursor: pointer; background: none; border: none; font-size: 18px; }
        .total-box { font-size: 24px; font-weight: bold; color: #1a5276; text-align: right; margin-top: 15px; }
        .input-block { margin-bottom: 15px; }
        .input-block label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        .input-block select, .input-block input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
    </style>
</head>
<body>

    <div class="main-container" style="max-width: 95%; margin: 0 auto; padding: 20px;">
        <div class="dashboard-header" style="display: flex; align-items: center; justify-content: space-between;">
            <a href="menuprincipal.php" class="nuevo" style="text-decoration: none;"><i class="ri-arrow-left-line"></i> Menú</a>
            <h1><i class="ri-shopping-cart-2-line" style="color: #3498db;"></i> Módulo de Caja / Nueva Venta</h1>
        </div>

        <div class="ventas-grid">
            
            <div class="panel-izquierdo">
                <h3>Añadir Prendas al Carrito</h3>
                <div class="selector-producto">
                    <div style="flex: 2;">
                        <label style="font-weight:bold; font-size:12px;">Selecciona la prenda:</label>
                        <select id="select-producto">
                            <option value="">-- Elige una prenda --</option>
                            <?php while($p = pg_fetch_assoc($res_productos)) { 
                                $detalles = "{$p['nombre']} ({$p['talla']} - {$p['color']}) - \${$p['precio']} [Stock: {$p['stock']}]";
                                echo "<option value='{$p['id_producto']}' data-precio='{$p['precio']}' data-stock='{$p['stock']}' data-nombre='{$p['nombre']}'>{$detalles}</option>";
                            } ?>
                        </select>
                    </div>
                    <div style="flex: 0.5;">
                        <label style="font-weight:bold; font-size:12px;">Cant:</label>
                        <input type="number" id="input-cantidad" value="1" min="1">
                    </div>
                    <button type="button" class="nuevo" onclick="agregarAlCarrito()" style="padding: 10px 15px;"><i class="ri-add-line"></i> Agregar</button>
                </div>

                <table class="tabla-carrito" id="tabla-carrito">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>

            <div class="panel-derecho">
                <h3>Finalizar Transacción</h3>
                <form id="form-venta" method="POST" action="ventas_salva.php" onsubmit="return prepararEnvio();">
                    
                    <div class="input-block">
                        <label for="id_cliente">Cliente (Opcional):</label>
                        <select name="id_cliente" id="id_cliente">
                            <option value="NULL">Público General</option>
                            <?php while($c = pg_fetch_assoc($res_clientes)) {
                                echo "<option value='{$c['id_cliente']}'>{$c['nombre']}</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="input-block">
                        <label for="metodo_pago">Método de Pago:</label>
                        <select name="metodo_pago" id="metodo_pago" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta de Débito">Tarjeta de Débito</option>
                            <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>

                    <input type="hidden" name="carrito_json" id="carrito_json">
                    <input type="hidden" name="total_venta" id="total_venta" value="0">

                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px;">
                        <p style="margin:0; font-size:14px; color:#555;">Total a pagar:</p>
                        <div class="total-box" id="txt-total">$0.00</div>
                    </div>

                    <button type="submit" class="nuevo" style="width: 100%; margin-top: 20px; padding: 12px; font-size:16px; background:#2e7d32; border-color:#2e7d32;">
                        <i class="ri-check-double-line"></i> Procesar y Cobrar
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>
<?php pg_close($con); ?>