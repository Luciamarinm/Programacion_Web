<?php
session_start(); //Inicia la sesión          
session_unset(); //Elimina las viariables de la sesión actual   
session_destroy(); //Destruye la sesión
header("Location: index.php"); //Redirige a index.php
exit(); //Termina la ejecución del script
?>