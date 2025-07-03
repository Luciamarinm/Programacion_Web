<?php
session_start(); //Inicia sesión
$server = "localhost"; //Dirección del servidor
$user = "root"; //Usuario de la base de datos
$pass = ""; //Contraseña de la base de datos
$db = "vetpet_db"; //Nombre de la base de datos
$conexion = new mysqli($server, $user, $pass, $db); //Conexión a la base de datos
if($conexion->connect_errno) { //Comprueba error de conexión
    die("Conexión fallida".$conexion->connect_errno); //Si hay error muestra mensaje
}
$conexion->set_charset("utf8"); //Caracteres para la conexión a UTF-8
?>