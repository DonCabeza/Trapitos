<?php
// 1. CONFIGURACIÓN Y CONEXIÓN
require_once 'funciones/conexion.php'; 
$con = conecta();

// =========================================================================
// 2. LOGICA DE ENVÍO DE DATOS (Se ejecuta solo al presionar "Guardar/Actualizar/Eliminar")
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
            <a href="Inventario.php?seccion=gc" class="accion">Gestionar Categorías</a>
            <a href="Inventario.php?seccion=registrar" class="nuevo">Nuevo Producto</a>
            <a href="MenuPrincipal.php" class="accion eliminar">Volver al Menú</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'exito'): ?>
                <div class="mensaje exito">¡Operación realizada correctamente!</div>
            <?php elseif ($_GET['msg'] === 'eliminado'): ?>
                <div class="mensaje exito">¡El producto fue dado de baja exitosamente!</div>
            <?php else: ?>
                <div class="mensaje error">
                    <strong>Error en la base de datos:</strong> <br>
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php 
        // =================================================================
        // SUB-RENDERIZADO: VER INVENTARIO (RF-14: Consultar por categoría)
        // =================================================================
        if ($seccion === 'ver'): 
            $filtro_cat = $_GET['filtro_cat'] ?? '';

            // 1. Buscamos todas las categorías existentes para armar el menú desplegable
            $query_cats = "SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria ASC";
            $res_cats = pg_query($con, $query_cats);
        ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--azul-principal); margin: 0;">
                    <?php echo ($filtro_cat !== '') ? "Mostrando: " . htmlspecialchars($filtro_cat) : "Todos los Productos"; ?>
                </h3>
                
                <form action="Inventario.php" method="GET" style="margin: 0; display: flex; gap: 10px; align-items: center;">
                    <input type="hidden" name="seccion" value="ver">
                    <label style="font-weight: 500; color: var(--azul-oscuro);">Filtrar:</label>
                    <select name="filtro_cat" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #ccc; outline: none;">
                        <option value="">-- Todas las categorías --</option>
                        <?php 
                        if ($res_cats) {
                            while ($cat = pg_fetch_assoc($res_cats)) {
                                // Mantenemos seleccionada la categoría que el usuario está viendo
                                $selected = ($filtro_cat === $cat['categoria']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($cat['categoria']) . '" ' . $selected . '>' . htmlspecialchars($cat['categoria']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <noscript><button type="submit" class="accion">Buscar</button></noscript>
                </form>
            </div>

            <?php
            // 2. Ejecutamos la consulta dependiendo de si hay filtro o no
            if ($filtro_cat !== '') {
                $query = "SELECT * FROM productos WHERE categoria = $1 ORDER BY id_producto DESC";
                $res = pg_query_params($con, $query, array($filtro_cat));
            } else {
                $query = "SELECT * FROM productos ORDER BY id_producto DESC";
                $res = pg_query($con, $query);
            }
            ?>

            <table class="Opciones">
                <thead>
                    <tr>
                        <th>ID</th><th>Nombre</th><th>Precio</th><th>Categoría</th><th>Stock</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res && pg_num_rows($res) > 0): while ($fila = pg_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $fila['id_producto']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td>$<?php echo $fila['precio']; ?></td>
                        <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
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
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" style="text-align:center;">No hay productos que coincidan con esta categoría.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php 
        // =================================================================
        // SUB-RENDERIZADO: GESTIONAR CATEGORÍAS (RF-04)
        // =================================================================
        elseif ($seccion === 'gc'): 
            // Buscamos todas las categorías únicas y contamos cuántos productos tienen
            $query_cat = "SELECT categoria, COUNT(id_producto) as total FROM productos WHERE categoria IS NOT NULL AND categoria != '' GROUP BY categoria ORDER BY categoria ASC";
            $res_cat = pg_query($con, $query_cat);
        ?>
            <div class="alta-form" style="margin: 0 auto; width: 80%;">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">Organizar Productos por Categoría</h3>
                    <a href="Inventario.php?seccion=registrar" class="nuevo" style="text-decoration: none; padding: 8px 15px; font-size: 14px;">+ Crear Categoría</a>
                </div>
                <table class="Opciones">
                    <thead>
                        <tr>
                            <th>Nombre de la Categoría</th>
                            <th>Total de Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res_cat && pg_num_rows($res_cat) > 0): while ($cat = pg_fetch_assoc($res_cat)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($cat['categoria']); ?></strong></td>
                            <td><?php echo $cat['total']; ?> productos</td>
                            <td>
                                <a href="Inventario.php?seccion=ver&filtro_cat=<?php echo urlencode($cat['categoria']); ?>" class="accion">Filtrar Productos</a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="3" style="text-align: center;">Aún no has asignado categorías a tus productos.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>-

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
                    
                    <input type="text" name="categoria" placeholder="Categoría (Ej. Pantalones)">
                    <input type="text" name="talla" placeholder="Talla (Ej. M, 32)">
                    <input type="text" name="color" placeholder="Color">
                    
                    <label style="display:block; text-align:left; margin-top:10px; font-size:14px; color:var(--azul-principal);">Proveedor:</label>
                    <select name="id_proveedor">
                        <option value="">-- Sin proveedor --</option>
                        <?php 
                        $query_prov = "SELECT id_proveedor, nombre FROM proveedores ORDER BY nombre ASC";
                        $res_prov = pg_query($con, $query_prov);
                        
                        if ($res_prov) {
                            while ($prov = pg_fetch_assoc($res_prov)) {
                                echo '<option value="' . $prov['id_proveedor'] . '">' . htmlspecialchars($prov['nombre']) . '</option>';
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
                    
                    <label style="display:block; text-align:left; margin-top:10px; font-size:14px; color:var(--azul-principal);">Proveedor:</label>
                    <select name="id_proveedor">
                        <option value="">-- Sin proveedor --</option>
                        <?php 
                        $query_prov = "SELECT id_proveedor, nombre FROM proveedores ORDER BY nombre ASC";
                        $res_prov = pg_query($con, $query_prov);
                        
                        if ($res_prov) {
                            while ($prov = pg_fetch_assoc($res_prov)) {
                                $seleccionado = ($producto['id_proveedor'] == $prov['id_proveedor']) ? 'selected' : '';
                                echo '<option value="' . $prov['id_proveedor'] . '" ' . $seleccionado . '>' . htmlspecialchars($prov['nombre']) . '</option>';
                            }
                        }
                        ?>
                    </select><br><br>
                    
                    <input type="submit" value="Actualizar Cambios" class="nuevo" style="width: 100%;">
                </form>
            </div>
        <?php endif; ?>
    </div> 
</body>
</html>