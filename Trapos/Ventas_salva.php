<?php
session_start();
require "funciones/conexion.php";
$con = conecta();

if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit();
}

// Recibir datos generales de la cabecera
$id_cliente = $_POST['id_cliente'];
$metodo_pago = $_POST['metodo_pago'] ?? 'Efectivo';
$total = $_POST['total_venta'] ?? 0;
$carrito_json = $_POST['carrito_json'] ?? '';

// Convertir el JSON del carrito de vuelta a un array de PHP
$carrito = json_decode($carrito_json, true);

if (empty($carrito)) {
    echo "<script>alert('Error: El carrito llegó vacío.'); window.history.back();</script>";
    exit();
}

// ============================================================
// INICIAMOS TRANSACCIÓN EN POSTGRESQL PARA PREVENIR ERRORES
// ============================================================
pg_query($con, "BEGIN");

// 1. Insertar la cabecera en la tabla 'ventas' (Aceptando si el cliente es NULL)
if ($id_cliente === "NULL" || empty($id_cliente)) {
    $sql_venta = "INSERT INTO ventas (metodo_pago, total) VALUES ($1, $2) RETURNING id_venta";
    $res_venta = pg_query_params($con, $sql_venta, array($metodo_pago, $total));
} else {
    $sql_venta = "INSERT INTO ventas (id_cliente, metodo_pago, total) VALUES ($1, $2, $3) RETURNING id_venta";
    $res_venta = pg_query_params($con, $sql_venta, array($id_cliente, $metodo_pago, $total));
}

if (!$res_venta) {
    pg_query($con, "ROLLBACK"); // Abortamos todo si falla
    echo "<script>alert('Error al registrar la cabecera de la venta.'); window.history.back();</script>";
    exit();
}

// Obtener el ID de la venta generado de forma automática por PostgreSQL
$fila_venta = pg_fetch_assoc($res_venta);
$id_venta = $fila_venta['id_venta'];

// 2. Recorrer cada artículo del carrito para guardarlo en 'detalles_venta' y actualizar el Stock
foreach ($carrito as $item) {
    $id_producto = $item['id_producto'];
    $cantidad = $item['cantidad'];
    $precio_unitario = $item['precio'];
    $subtotal = $item['subtotal'];

    // A) Insertar fila en detalles_venta
    $sql_detalle = "INSERT INTO detalles_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES ($1, $2, $3, $4, $5)";
    $res_detalle = pg_query_params($con, $sql_detalle, array($id_venta, $id_producto, $cantidad, $precio_unitario, $subtotal));

    if (!$res_detalle) {
        pg_query($con, "ROLLBACK");
        echo "<script>alert('Error al procesar el detalle de los artículos.'); window.history.back();</script>";
        exit();
    }

    // B) MODIFICACIÓN AUTOMÁTICA DE INVENTARIO: Restar la cantidad vendida al stock de la prenda
    $sql_update_stock = "UPDATE productos SET stock = stock - $1 WHERE id_producto = $2";
    $res_stock = pg_query_params($con, $sql_update_stock, array($cantidad, $id_producto));

    if (!$res_stock) {
        pg_query($con, "ROLLBACK");
        echo "<script>alert('Error crítico al intentar descontar las piezas del inventario.'); window.history.back();</script>";
        exit();
    }
}

// Si todos los inserts y updates pasaron sin problemas, guardamos permanentemente en la base de datos
pg_query($con, "COMMIT");

echo "<script>alert('Venta procesada y cobrada con éxito. Inventario actualizado.'); window.location.href='ventas_nueva.php';</script>";

pg_close($con);
?>