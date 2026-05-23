<?php
session_start();

// Validamos que solo el administrador pueda hacer respaldos (según la lógica que tienes en MenuPrincipal)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    // Si no es admin, lo regresamos al menú
    header("Location: MenuPrincipal.php");
    exit;
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'respaldar') {
    
    // =================================================================
    // 1. CONFIGURACIÓN DEL SERVIDOR Y BASE DE DATOS
    // =================================================================
    $host = "localhost";
    $usuario = "postgres";
    
    // ATENCIÓN: Pon aquí tu contraseña real de PostgreSQL
    $password = "210905"; 
    
    // El nombre de tu base de datos
    $base_datos = "trapitos_Db"; 

    // =================================================================
    // 2. RUTA DE LA HERRAMIENTA PG_DUMP (Para Windows)
    // =================================================================
    // Revisa en tu disco C: qué versión de PostgreSQL tienes instalada. 

    $ruta_pg_dump = '"C:\Program Files\PostgreSQL\18\bin\pg_dump.exe"'; 

    // Generamos un nombre para el archivo con la fecha y hora exacta
    $fecha_actual = date("Y-m-d_H-i-s");
    $nombre_archivo = "Respaldo_Trapitos_" . $fecha_actual . ".sql";
    
    // Esta es la ruta temporal dentro de tu carpeta htdocs donde se creará el archivo
    $ruta_guardado = __DIR__ . '/' . $nombre_archivo;

    // =================================================================
    // 3. EJECUCIÓN DEL RESPALDO
    // =================================================================
    // Pasamos la contraseña como variable de entorno temporal a Windows (es la forma segura)
    putenv("PGPASSWORD=" . $password);
    
    // Armamos el comando de Windows
    $comando = "$ruta_pg_dump -h $host -U $usuario -F p -f \"$ruta_guardado\" $base_datos 2>&1";
    
    // shell_exec manda la orden a la terminal de Windows en segundo plano
    shell_exec($comando);

    // =================================================================
    // 4. DESCARGA AUTOMÁTICA DEL ARCHIVO
    // =================================================================
    if (file_exists($ruta_guardado) && filesize($ruta_guardado) > 0) {
        // Le decimos al navegador web que esto es una descarga de archivo
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($ruta_guardado) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($ruta_guardado));
        
        // Leemos el archivo y lo enviamos al navegador
        readfile($ruta_guardado);
        
        // ¡Súper importante! Borramos el archivo de la carpeta htdocs para no saturar el servidor
        unlink($ruta_guardado);
        exit;
    } else {
        $mensaje = "Error al generar el respaldo. Revisa tu contraseña y la ruta de pg_dump en el código.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respaldo del Sistema | Mis Trapitos</title>
    <link rel="stylesheet" href="estilo/estilo.css">
</head>
<body>

    <div class="menu-contenedor" style="width: 50%; margin: 50px auto; text-align: center;">
        
        <div class="titulo">
            <h2>Módulo de Seguridad</h2>
            <p>Generación de Respaldo de Base de Datos</p>
        </div>

        <?php if ($mensaje !== ""): ?>
            <div class="mensaje error" style="margin-bottom: 20px;">
                <strong>Aviso:</strong> <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div style="margin: 30px 0; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #ddd;">
            <p style="color: var(--azul-oscuro); margin-bottom: 20px;">
                Al hacer clic en el botón, el sistema empaquetará todo el inventario, ventas y usuarios en un archivo <strong>.sql</strong> seguro.
            </p>

            <form action="Rerespaldo.php" method="POST">
                <input type="hidden" name="accion" value="respaldar">
                <button type="submit" class="nuevo" style="font-size: 16px; padding: 15px 30px; cursor: pointer; border: none; width: 100%;">
                    📥 Descargar Respaldo Ahora
                </button>
            </form>
        </div>

        <div class="boton" style="text-align: center; margin-top: 20px;">
            <a href="MenuPrincipal.php" class="accion eliminar">Volver al Menú</a>
        </div>

    </div>

</body>
</html>