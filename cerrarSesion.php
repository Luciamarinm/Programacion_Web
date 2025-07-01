<?php
session_start();          // Inicia la sesión
session_unset();          // Elimina todas las variables de sesión
session_destroy();        // Destruye la sesión
header("Location: index.php"); // Redirige al login (u otra página)
exit();
?>