<?php
session_start();
// Control de acceso y seguridad (RF-17)
if (!isset($_SESSION['rol'])) {
    header("Location: index.php");
    exit;
}

$rol = $_SESSION['rol'];
$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="ImageL.png">
    <link rel="stylesheet" href="estilo/estilo.css">
</head>
<body>
    <div class="menu-contenedor">

        <div class="titulo">
            <h2>Tienda de Ropa "Mis Trapitos" - Sistema de Gestión</h2>
            <p>Bienvenido(a), <b><?php echo htmlspecialchars($usuario); ?></b> (<?php echo htmlspecialchars($rol); ?>)</p>
        </div>

        <div class="boton" style="text-align: right;">
            <a href="CerrarSesion.php" class="nuevo">Cerrar sesión</a>
        </div>

        <?php if ($rol === "administrador") { ?>
        <!-- ================================================================= -->
        <!-- ADMINISTRADOR                                                     -->
        <!-- ================================================================= -->

            <h3>Menú de Administración</h3>
            <table class="Opciones">
                <tr><th colspan="2">Almacen E Inventario</th></tr>
                <tr>
                    <td><a href="inventario.php?=registrar" class="accion">Registrar Producto</a></td>
                    <td><a href="inventario.php?=ver" class="accion">Ver Inventario</a></td>
                </tr>
                <tr>
                    <td><a href="inventario.php?=modificar" class="accion">Modificar Producto</a></td>
                    <td><a href="inventario.php?=eliminar" class="accion eliminar">Dar de Baja</a></td>
                </tr>
                <tr>
                    <td colspan="2"><a href="productos_categorias.php" class="accion">Gestionar Categorías</a></td>
                </tr>
                <tr><th colspan="2">Ventas y caja</th></tr>
                <tr>
                    <td><a href="ventas_nueva.php" class="accion">Nueva Venta</a></td>
                    <td><a href="inventario_consulta.php" class="accion">Consultar Disponibilidad</a></td>
                </tr>
                <tr>
                    <td colspan="2"><a href="promociones_lista.php?filtro=activas" class="accion">Ver Promociones Activas</a></td>
                </tr>
                <tr><th colspan="2">Reportes Y Sistema</th></tr>
                <tr>
                    <td><a href="reporte_diario.php" class="accion">Reporte Diario</a></td>
                    <td><a href="reporte_mensual.php" class="accion">Reporte Mensual</a></td>
                </tr>
                <tr>
                                    <td><a href="ReRespaldo.php" class="accion eliminar">Realizar Respaldo BD</a></td>
                </tr>
                                <tr><th colspan="2">Gestion de empleados</th></tr>
                <tr>
                    <td><a href="usuarios_alta.php" class="accion">Añadir empleado</a></td>
                    <td><a href="Usuarios_lista.php" class="accion">Mostrar empleados </a></td>
                </tr>
                <tr>
                     <td><a href="usuarios_modificar.php" class="accion">Modificar empleado </a></td>
                    <td><a href="usuarios_gestion.php" class="accion eliminar">Eliminar empleado</a></td>
                   
                </tr>
                                <tr><th colspan="2">Gestionar promociones</th></tr>
                <tr>
                    <td><a href="promociones_alta.php" class="accion">Registrar Promoción</a></td>
                    <td><a href="promociones_lista.php?filtro=todas" class="accion">Ver Promociones</a></td>
                </tr>
                <tr>
                    <td><a href="productos_modifica.php" class="accion">Modificar Producto</a></td>
                    <td><a href="productos_elimina.php" class="accion eliminar">Dar de Baja</a></td>
                </tr>
                  <tr><th colspan="2">PROVEEDORES</th></tr>
                <tr>
                    <td><a href="proveedores_alta.php" class="accion">Añadir Proveedores</a></td>
                    <td><a href="proveedores_productos.php" class="accion">Productos por Proveedor</a></td>
                </tr>
                   <tr>
                    <td><a href="proveedores_lista.php" class="accion">Directorio de Proveedores</a></td>
        
                </tr>
                 <tr><th colspan="2">CLIENTES</th></tr>
                <tr>
                    <td><a href="Clientes.php" class="accion">Registrar Cliente</a></td>
                    <td><a href="Clientes.php?seccion=ver" class="accion">Historial de Compras</a></td>
                </tr>
                <tr><th colspan="2">REPORTES</th></tr>
                <tr>
                    <td><a href="reportes.php" class="accion">Generar reporte de ventas</a></td>
                </tr>

            </table>

        <?php } elseif ($rol === "empleado") { ?>
        <!-- ================================================================= -->
        <!-- VENDEDOR                                                          -->
        <!-- ================================================================= -->
            <h3>Menú del Personal (Vendedor)</h3>
            <table class="Opciones">
                <tr><th colspan="2">VENTAS Y CAJA</th></tr>
                <tr>
                    <td><a href="ventas_nueva.php" class="accion">Nueva Venta</a></td>
                    <td><a href="inventario_consulta.php" class="accion">Consultar Disponibilidad</a></td>
                </tr>
                <tr>
                    <td colspan="2"><a href="promociones_lista.php?filtro=activas" class="accion">Ver Promociones Activas</a></td>
                </tr>

                <tr><th colspan="2">CLIENTES</th></tr>
                <tr>
                    <td><a href="Clientes.php" class="accion">Registrar Cliente</a></td>
                    <td><a href="Clientes.php?seccion=ver" class="accion">Historial de Compras</a></td>
                </tr>

                <tr><th colspan="2">PROVEEDORES</th></tr>
                <tr>
                    <td><a href="proveedores_lista.php" class="accion">Directorio de Proveedores</a></td>
                    <td><a href="proveedores_productos.php" class="accion">Productos por Proveedor</a></td>
                </tr>
                                <tr><th colspan="2">PRODUCTOS</th></tr>
                <tr>
                    <td><a href="inventario.php?=registrar" class="accion">Registrar Producto</a></td>
                    <td><a href="inventario.php?=ver" class="accion">Ver Inventario</a></td>
                </tr>
                <tr>
                    <td><a href="inventario.php?=modificar" class="accion">Modificar Producto</a></td>
                    <td><a href="inventario.php?=eliminar" class="accion eliminar">Dar de Baja</a></td>
                </tr>
                <tr>
                    <td colspan="2"><a href="inventario.php?seccion=gc" class="accion">Gestionar Categorías</a></td>
                </tr>
                                <tr><th colspan="2">DESCUENTOS</th></tr>
                <tr>
                    <td><a href="promociones_lista.php?filtro=todas" class="accion">Lista de promociones</a></td>
                </tr>
               
                


            </table>
            
        <?php } else { ?>
            <p class="mensaje error" style="color:red; text-align:center; font-weight:bold; margin-top:20px;">
                Rol de usuario no reconocido o sin privilegios locales.
            </p>
        <?php } ?>
    </div>
</body>
</html>
