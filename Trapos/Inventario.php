<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
// ¡Asegúrate de que la variable dentro de este archivo se llame $conexion!
require_once 'funciones/conexion.php'; 
$con = conecta();
// =========================================================================
// 2. LOGICA DE ENVÍO DE DATOS (Se ejecuta solo al presionar "Guardar/Actualizar")
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'registrar') {
        $stock = !empty($_POST['stock']) ? $_POST['stock'] : 0;
        $id_proveedor = !empty($_POST['id_proveedor']) ? $_POST['id_proveedor'] : null;

        $query = 'INSERT INTO productos (nombre, precio, stock, categoria, talla, color, id_proveedor) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7)';
                  
        $parametros = array(
            $_POST['nombre'], $_POST['precio'], $stock, 
            $_POST['categoria'], $_POST['talla'], $_POST['color'], $id_proveedor
        );
        
        $resultado = pg_query_params($con, $query, $parametros);
        
        if ($resultado) {
            header("Location: Inventario.php?seccion=ver&msg=exito");
        } else {
            $error_db = pg_last_error($con);
            header("Location: Inventario.php?seccion=registrar&msg=" . urlencode($error_db));
        }
        exit(); 
    }

    if ($accion === 'modificar') {
        $stock = !empty($_POST['stock']) ? $_POST['stock'] : 0;
        $id_proveedor = !empty($_POST['id_proveedor']) ? $_POST['id_proveedor'] : null;
        $id_producto = $_POST['id_producto'];

        $query = 'UPDATE productos 
                  SET nombre = $1, precio = $2, stock = $3, categoria = $4, talla = $5, color = $6, id_proveedor = $7 
                  WHERE id_producto = $8';
                  
        $parametros = array(
            $_POST['nombre'], $_POST['precio'], $stock, $_POST['categoria'], 
            $_POST['talla'], $_POST['color'], $id_proveedor, $id_producto
        );
        
        $resultado = pg_query_params($con, $query, $parametros);
        
        if ($resultado) {
            header("Location: Inventario.php?seccion=ver&msg=exito");
        } else {
            $error_db = pg_last_error($con);
            header("Location: Inventario.php?seccion=modificar&id_producto=$id_producto&msg=" . urlencode($error_db));
        }
        exit();
    }

    if ($accion === 'eliminar') {
        $id_producto = $_POST['id_producto'];

        $query = 'DELETE FROM productos WHERE id_producto = $1';
        $resultado = pg_query_params($con, $query, array($id_producto));
        
        if ($resultado) {
            header("Location: Inventario.php?seccion=ver&msg=eliminado");
        } else {
            $error_db = pg_last_error($con);
            header("Location: Inventario.php?seccion=ver&msg=" . urlencode($error_db));
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
    <title>Módulo de Inventario - Mis Trapitos</title>
    <link rel="stylesheet" href="estilo/Estilo.css"> 
    <link rel="stylesheet" href="estilo/listas.css"> 
</head>
<body>

    <div class="menu-contenedor">
        
        <h1 class="titulo">Gestión de Productos</h1>
        
        <div class="boton">
            <a href="Inventario.php?seccion=ver" class="accion">Ver Inventario</a>
            <a href="Inventario.php?seccion=registrar" class="nuevo">Nuevo Producto</a>
            <a href="MenuPrincipal.php" class="accion eliminar">Volver al Menú</a>
        </div>

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
        // SUB-RENDERIZADO: VER INVENTARIO
        // =================================================================
        if ($seccion === 'ver'): 
            $query = "SELECT * FROM productos ORDER BY id_producto DESC";
            $res = pg_query($con, $query);
        ?>
            <table class="Opciones">
                <thead>
                    <tr>
                        <th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res): while ($fila = pg_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $fila['id_producto']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td>$<?php echo $fila['precio']; ?></td>
                        <td><?php echo $fila['stock']; ?></td>
                        <td>
                            <div class="funciones">
                                <a href="Inventario.php?seccion=modificar&id_producto=<?php echo $fila['id_producto']; ?>" class="accion">Editar</a>
                                
                                <form action="Inventario.php" method="POST" style="margin: 0;" onsubmit="return confirm('¿Estás seguro de que deseas dar de baja este producto de forma permanente?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_producto" value="<?php echo $fila['id_producto']; ?>">
                                    
                                    <button type="submit" class="accion eliminar" style="cursor: pointer;">Dar de Baja</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>

        <?php 
        // =================================================================
        // SUB-RENDERIZADO: FORMULARIO DE REGISTRO
        // =================================================================
        elseif ($seccion === 'registrar'): 
        ?>
            <div class="alta-form" style="margin: 0 auto;">
                <h3>Registrar Nuevo Producto</h3>
                <form action="Inventario.php" method="POST">
                    <input type="hidden" name="accion" value="registrar">
                    
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="number" step="0.01" name="precio" placeholder="Precio" required>
                    <input type="number" name="stock" placeholder="Stock Inicial">
                    <input type="text" name="categoria" placeholder="Categoría">
                    <input type="text" name="talla" placeholder="Talla">
                    <input type="text" name="color" placeholder="Color">
                    <label>Proveedor:</label>
                    <select name="id_proveedor">
                        <option value="">-- Sin proveedor --</option>
                        <?php 
                        $query_prov = "SELECT id_proveedor, nombre FROM proveedores ORDER BY nombre ASC";
                        $res_prov = pg_query($con, $query_prov);
                        
                        if ($res_prov) {
                            while ($prov = pg_fetch_assoc($res_prov)) {
                                echo '<option value="' . $prov['id_proveedor'] . '">' . 
                                    htmlspecialchars($prov['nombre']) . 
                                    '</option>';
                            }
                        }
                        ?>
                    </select><br><br> 
                    <input type="submit" value="Guardar en Base de Datos" class="nuevo" style="width: 100%;">
                </form>
            </div>

        <?php 
        // =================================================================
        // SUB-RENDERIZADO: FORMULARIO DE EDICIÓN
        // =================================================================
        elseif ($seccion === 'modificar'): 
            $id_producto = $_GET['id_producto'] ?? 0;
            
            $query = "SELECT * FROM productos WHERE id_producto = $1";
            $res = pg_query_params($con, $query, array($id_producto));
            $producto = pg_fetch_assoc($res);
        ?>
            <div class="alta-form" style="margin: 0 auto;">
                <h3>Editar Producto #<?php echo $id_producto; ?></h3>
                <form action="Inventario.php" method="POST">
                    <input type="hidden" name="accion" value="modificar">
                    <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                    
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                    <input type="number" step="0.01" name="precio" value="<?php echo $producto['precio']; ?>" required>
                    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>">
                    <input type="text" name="categoria" value="<?php echo htmlspecialchars($producto['categoria']); ?>">
                    <input type="text" name="talla" value="<?php echo htmlspecialchars($producto['talla']); ?>">
                    <input type="text" name="color" value="<?php echo htmlspecialchars($producto['color']); ?>">
                    <input type="number" name="id_proveedor" value="<?php echo $producto['id_proveedor']; ?>">
                    
                    <input type="submit" value="Actualizar Cambios" class="nuevo" style="width: 100%;">
                </form>
            </div>
        <?php endif; ?>
    </div> </body>
</html>