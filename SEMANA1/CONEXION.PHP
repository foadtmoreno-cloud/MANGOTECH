<?php
$CONEXION = mysqli_connect("localhost", "root", "", "sistema_ventas");

if (!$CONEXION) {
    die("Error de conexion: " . mysqli_connect_error());
}
?>