<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit;
}

include("funciones/conexion.php");
$con = conecta();

$query = "SELECT id_usuario, username, rol 
          FROM usuarios 
          WHERE rol = 'empleado' 
          ORDER BY id_usuario";

$resultado = pg_query($con, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Personal | Mis Trapitos</title>
    <link rel="icon" type="image/png" href="Imagenes/icono.png">
    <link rel="stylesheet" href="estilo/estilo.css">
    <link rel="stylesheet" href="estilo/listas.css">
</head>
<body>
    <div class="menu-contenedor">
        <h1>Personal Registrado</h1>

        <table>
            <tr>
                <th>ID Usuario</th>
                <th>Nombre de Usuario (Login)</th>
                <th>Rol asignado</th>
            </tr>

            <?php
            if ($resultado && pg_num_rows($resultado) > 0) {
                while ($fila = pg_fetch_assoc($resultado)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($fila['id_usuario']) . "</td>";
                    echo "<td>" . htmlspecialchars($fila['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($fila['rol']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay empleados registrados actualmente.</td></tr>";
            }
            pg_close($con);
            ?>
        </table>

        <br>
        <a href="MenuPrincipal.php" class="nuevo">Volver al menú</a>
    </div>
</body>
</html>