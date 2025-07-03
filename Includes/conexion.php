<?php
class Conexion {
    function getConectar() {
        $server = "localhost:3306";
        $login = "root";
        $clave = "";
        $bd = "newdb_titishop";

        $cn = mysqli_connect($server, $login, $clave, $bd);

        if (!$cn) {
            die("Error al conectar: " . mysqli_connect_error());
        }

        return $cn;
    }
}
?>