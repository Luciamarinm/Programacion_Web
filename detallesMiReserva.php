<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) { //Si no ha iniciado sesión redirige a index.php
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Cliente') {//Si no ha iniciado sesión como Cliente redirige a index.php
    header("Location: index.php");
    exit();
}

if (isset($_GET['idReserva'])) {//Recoge el idReserva de la URL
    $idReserva = intval($_GET['idReserva']); //Convierte a entero por seguridad
} else {
    echo "<script>alert('Reserva no especificada'); window.location = 'miPerfil.php';</script>";
    exit;
}
//Select para mostrar la reserva con ese id
$sqlReserva = "SELECT * FROM reservas WHERE idReserva = $idReserva";
$resultadoReserva = $conexion->query($sqlReserva);
if ($resultadoReserva && $resultadoReserva->num_rows > 0) {
    $reserva = $resultadoReserva->fetch_assoc();
    //Los resultados de la select
    $idUsuario = $reserva['idUsuario'];
    $idMascota = $reserva['idMascota'];
    $email = $reserva['email'];
    $telefono = $reserva['telefono'];
    $servicio = $reserva['servicio'];
    $telefono = $reserva['telefono'];
    $fecha = $reserva['fecha'];
    $hora = $reserva['hora'];
    $mensaje = $reserva['mensaje'];
    $diagnostico = $reserva['diagnostico'];
} else {
    echo "<p>Reserva no encontrada.</p>";
}
//Select para cargar datos de la mascota de la reserva
$sqlUsuario = "SELECT * FROM mascotas WHERE idMascota = $idMascota";
$resultadoMascota = $conexion->query($sqlUsuario);
if ($resultadoMascota && $resultadoMascota->num_rows > 0) {
    $mascota = $resultadoMascota->fetch_assoc();
    $nombreMascota= $mascota['nombreMascota'];
} else {
    echo "<p>Usuario no encontrado.</p>";
}
//Lógica para borrar la reserva
if (isset($_GET['borrar']) && is_numeric($_GET['borrar'])) {
    $idReservaABorrar = intval($_GET['borrar']);
    //Buscamos si existe
    $sqlCheck = "SELECT * FROM reservas WHERE idReserva = $idReservaABorrar AND idMascota = $idMascota";
    $resCheck = $conexion->query($sqlCheck);
    if ($resCheck && $resCheck->num_rows > 0) {
        //Hacemos el delete de la reserva
        $sqlDelete = "DELETE FROM reservas WHERE idReserva = $idReservaABorrar";
        if ($conexion->query($sqlDelete)) {
            echo "<script>alert('Reserva borrada con éxito'); 
            window.location.href='miPerfil.php';</script>";//Redirige a miPerfil.php
            exit;
        } else {//Nos quedamos en la página
            echo "<script>alert('Error al borrar la reserva'); 
            window.location.href='".$_SERVER['PHP_SELF']."';</script>";
            exit;
        }
    } else {//En caso de no tener permiso de borrar esa reserva
        echo "<script>alert('No tienes permiso para borrar esta reserva'); window.location.href='".$_SERVER['PHP_SELF']."';</script>";
        exit;
    }
}
?>
<main class="contenido-principal">
<main class="container">
    <section class="row">
    <section class="col">
    </section>
    <section class="col-10">
            <!--Servicio, fecha y hora de la reserva-->
            <h1 class="hIzquierda"><?php echo $servicio." ".$fecha." ".$hora ?>
            <!--Enlace a formulario de modificar esa reserva-->
            <a href="modificarMiReserva.php?idReserva=<?php echo $idReserva; ?>"><i class="bi bi-pencil"></i></a>
            <!--Enlace a borrar esa reserva-->
            <a href="?idReserva=<?php echo $idReserva; ?>&borrar=<?php echo $idReserva; ?>"
                onclick="return confirm('¿Seguro que quieres borrar esta reserva?');">
                <i class="bi bi-trash"></i></a>
            <h2 class="hIzquierda">Mascota: <?php echo $nombreMascota ?></h2>
            <h2 class="hIzquierda">Mensaje: <?php echo $mensaje ?></h2>
            <h2 class="hIzquierda">Diagnóstico: <?php echo $diagnostico ?></h2>
    </section>
    <section class="col">
    </section>
    </section>
    </main>
</main>  
<?php
include_once 'footer.php';
?>