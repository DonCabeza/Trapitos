
<?php

define("HOST", 'localhost');define("PORT", '5432');
define("BD", 'trapitos_db');
define("USER_BD", 'postgres');
define("PASS_BD", '1234');

function conecta() {
    $cadenaConexion = "host=" . HOST . " port=" . PORT . " dbname=" . BD . " user=" . USER_BD . " password=" . PASS_BD;
    $con = pg_connect($cadenaConexion);

    if (!$con) {
        die("<p>Error de conexión a PostgreSQL.</p>");
    }

    return $con;
}
?> 