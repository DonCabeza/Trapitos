<?php
session_start();
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

include("funciones/conexion.php");
$con = conecta();

// ============================================================
// LÓGICA DE PROCESAMIENTO: UPDATE (MÉTODO POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'actualizar') {
    // Restricción: Solo el administrador puede alterar los datos de origen
    if ($_SESSION['rol'] !== 'administrador') {
        echo "<script>alert('Acceso denegado: Solo administradores pueden modificar proveedores.'); window.location.href='proveedores_lista.php';</script>";
        exit();
    }

    $id_proveedor = $_POST['id_proveedor'];
    $nombre       = trim($_POST['nombre'] ?? '');
    $telefono     = trim($_POST['telefono'] ?? '');
    $correo       = trim($_POST['correo'] ?? '');

    if (empty($nombre)) {
        echo "<script>alert('El nombre del proveedor es un campo obligatorio.'); window.history.back();</script>";
        exit();
    }

    // Consulta segura de actualización
    $sql_update = "UPDATE proveedores 
                   SET nombre = $1, telefono = $2, correo = $3 
                   WHERE id_proveedor = $4";
    
    $res_update = pg_query_params($con, $sql_update, array($nombre, $telefono, $correo, $id_proveedor));

    if ($res_update) {
        echo "<script>alert('Proveedor actualizado con éxito.'); window.location.href='proveedores_lista.php';</script>";
        exit();
    } else {
        $error = pg_last_error($con);
        echo "<script>alert('Error al actualizar en la base de datos: " . addslashes($error) . "'); window.history.back();</script>";
    }
}

// ============================================================
// LÓGICA DE DETECCIÓN: CARGAR DATOS PARA EDICIÓN (MÉTODO GET)
// ============================================================
$prov_editar = null;
if (isset($_GET['editar_id']) && $_SESSION['rol'] === 'administrador') {
    $id_buscado = $_GET['editar_id'];
    $sql_busca = "SELECT * FROM proveedores WHERE id_proveedor = $1 AND eliminado = FALSE";
    $res_busca = pg_query_params($con, $sql_busca, array($id_buscado));
    
    if (pg_num_rows($res_busca) > 0) {
        $prov_editar = pg_fetch_assoc($res_busca);
    }
}


if (isset($_GET['eliminar_id'])) {
    if ($_SESSION['rol'] !== 'administrador') {
        echo "<script>alert('Acceso denegado: Solo administradores pueden eliminar proveedores.'); window.location.href='proveedores_lista.php';</script>";
        exit();
    }

    $id_eliminar = $_GET['eliminar_id'];

    // Cambiamos el switch lógico a TRUE para ocultarlo en las consultas del sistema
    $sql_delete = "UPDATE proveedores SET eliminado = TRUE WHERE id_proveedor = $1";
    $res_delete = pg_query_params($con, $sql_delete, array($id_eliminar));

    if ($res_delete) {
        echo "<script>alert('Proveedor eliminado correctamente.'); window.location.href='proveedores_lista.php';</script>";
        exit();
    } else {
        $error = pg_last_error($con);
        echo "<script>alert('Error al eliminar: " . addslashes($error) . "'); window.history.back();</script>";
    }
}

// ============================================================
// CONSULTA GENERAL: SOLO FILTRA LOS QUE NO HAN SIDO ELIMINADOS
// ============================================================
$query = "SELECT id_proveedor, nombre, telefono, correo FROM proveedores WHERE eliminado = FALSE ORDER BY nombre ASC";
$resultado = pg_query($con, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores | Mis Trapitos</title>
   <link rel="icon" type="image/png" href="ImageL.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link rel="stylesheet" href="estilo/listas.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">

</head>
<body>
    <div class="menu-contenedor" style="max-width: 95%; margin: 20px auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <a href="MenuPrincipal.php" class="nuevo" style="text-decoration: none;"><i class="ri-arrow-left-line"></i> Menú</a>
            <h1 style="margin: 0;"><i class="ri-truck-line" style="color: #3498db;"></i> Directorio de Proveedores</h1>
            <?php if ($_SESSION['rol'] === 'administrador'): ?>
                <a href="proveedores_alta.php" class="nuevo" style="text-decoration: none; background: #27ae60; border-color: #27ae60;"><i class="ri-add-line"></i> Nuevo Proveedor</a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>
        </div>

        <?php if ($prov_editar && $_SESSION['rol'] === 'administrador'): ?>
            <div class="form-edicion-rapida">
                <h3 style="color: #2980b9; margin-top:0;"><i class="ri-edit-box-line"></i> Modificar Ficha de Proveedor</h3>
                <p style="margin: 0; font-size: 13px; color: #7f8c8d;">ID Registro Local: <b><?= htmlspecialchars($prov_editar['id_proveedor']) ?></b></p>
                
                <form method="POST" action="proveedores_lista.php">
                    <input type="hidden" name="action" value="actualizar">
                    <input type="hidden" name="id_proveedor" value="<?= $prov_editar['id_proveedor'] ?>">
                    
                    <div class="grid-campos">
                        <div class="campo-edit">
                            <label>Nombre de la Empresa o Contacto:</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($prov_editar['nombre']) ?>" required>
                        </div>
                        <div class="campo-edit">
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" value="<?= htmlspecialchars($prov_editar['telefono']) ?>">
                        </div>
                        <div class="campo-edit">
                            <label>Correo Electrónico:</label>
                            <input type="email" name="correo" value="<?= htmlspecialchars($prov_editar['correo']) ?>">
                        </div>
                    </div>
                    
                    <div style="margin-top: 15px; text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
                        <a href="proveedores_lista.php" class="btn-can"><i class="ri-close-line"></i> Cancelar</a>
                        <button type="submit" class="btn-mod"><i class="ri-save-line"></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre de la Empresa / Contacto</th>
                <th>Teléfono</th>
                <th>Correo Electrónico</th>
                <?php if ($_SESSION['rol'] === 'administrador'): ?> <th style="text-align: center;">Acciones</th> <?php endif; ?>
            </tr>

            <?php
            if ($resultado && pg_num_rows($resultado) > 0) {
                while ($fila = pg_fetch_assoc($resultado)) { ?>
                    <tr>
                        <td><b><?= htmlspecialchars($fila['id_proveedor']) ?></b></td>
                        <td><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td><?= htmlspecialchars($fila['telefono'] ? $fila['telefono'] : 'No registrado') ?></td>
                        <td><?= htmlspecialchars($fila['correo'] ? $fila['correo'] : 'No registrado') ?></td>
                        
                        <?php if ($_SESSION['rol'] === 'administrador'): ?>
                            <td style="text-align: center; display: flex; justify-content: center; gap: 15px;">
                                <a href="proveedores_lista.php?editar_id=<?= $fila['id_proveedor'] ?>" style="color: #3498db; font-weight: bold; text-decoration: none;">
                                    <i class="ri-pencil-line"></i> Editar
                                </a>
                                
                                <a href="proveedores_lista.php?eliminar_id=<?= $fila['id_proveedor'] ?>" 
                                   style="color: #e74c3c; font-weight: bold; text-decoration: none;" 
                                   onclick="return confirm('¿Estás seguro de que deseas dar de baja a este proveedor del directorio activo?');">
                                    <i class="ri-delete-bin-line"></i> Eliminar
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="<?= $_SESSION['rol'] === 'administrador' ? '5' : '4' ?>" style="text-align: center; padding: 30px; color: #888;">
                        No hay proveedores activos registrados en el sistema.
                    </td>
                </tr>
            <?php } pg_close($con); ?>
        </table>
    </div>
</body>
</html>