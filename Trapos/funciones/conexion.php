
<?php

define("HOST", 'localhost');define("PORT", '5432');
define("BD", 'trapitos_Db');
define("USER_BD", 'usuario_php');
define("PASS_BD", '123456');

function conecta() {
    $cadenaConexion = "host=" . HOST . " port=" . PORT . " dbname=" . BD . " user=" . USER_BD . " password=" . PASS_BD;
    $con = pg_connect($cadenaConexion);

    if (!$con) {
        die("<p>Error de conexión a PostgreSQL.</p>");
    }

    return $con;
}
?> 