<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) { //Si no ha iniciado sesión redirige a index.php
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {//Si no ha iniciado sesión como Admin redirige a index.php
    header("Location: index.php");
    exit();
}

if (isset($_GET['idMascota'])) {//Recoge el idMascota de la URL
    $idMascota = intval($_GET['idMascota']); //Convierte a entero por seguridad
} else {
    echo "<script>alert('Mascota no especificada'); window.location = 'gestion.php';</script>";
    exit;
}
//Select para mostrar la mascota con ese id
$sql = "SELECT * FROM mascotas WHERE idMascota = $idMascota";
$resultado = $conexion->query($sql);
//Los resultados de la select
if ($resultado && $resultado->num_rows > 0) {
    $mascota = $resultado->fetch_assoc();
    $nombre= $mascota['nombreMascota'];
    $tipo= $mascota['tipoAnimal'];
    $raza= $mascota['raza'];
    $nacimiento= $mascota['fechaNacimiento'];
    $historial= $mascota['Historial'];
    $idUsuario= $mascota['idUsuario'];
} else {
    echo "<p>Mascota no encontrada.</p>";
}
//Select para ver los usuarios con ese id
$sqlUsuario = "SELECT * FROM usuarios WHERE idUsuario = $idUsuario";
$resultadoUsuario = $conexion->query($sqlUsuario);
//Los resultados de la select
if ($resultadoUsuario && $resultadoUsuario->num_rows > 0) {
    $usuario = $resultadoUsuario->fetch_assoc();
    $idUsuario= $usuario['idUsuario'];
    $nombreUsuario= $usuario['nombreUsuario'];
    $apellidosUsuario= $usuario['apellidosUsuario'];
    $DNIUsuario= $usuario['DNI'];
} else {
    echo "<p>Usuario no encontrado.</p>";
}

//Lógica de borrar mascota
if (isset($_GET['borrar']) && is_numeric($_GET['borrar'])) { 
    $idMascotaABorrar = intval($_GET['borrar']); //Recogemos el id a borrar como int
    //Buscamos si existe
    $sqlCheck = "SELECT * FROM mascotas WHERE idMascota = $idMascotaABorrar";
    $resCheck = $conexion->query($sqlCheck);
    if ($resCheck && $resCheck->num_rows > 0) {
        //Hacemos el delete de reservas de esa mascota
        $sqlDeleteReservas2 = "DELETE FROM reservas WHERE idMascota = $idMascotaABorrar";
        $conexion->query($sqlDeleteReservas2);
        //Hacemos el delete de esa mascota
        $sqlDelete = "DELETE FROM mascotas WHERE idMascota = $idMascotaABorrar";
        if ($conexion->query($sqlDelete)) {
            echo "<script>alert('Mascota borrada con éxito'); 
            window.location.href='gestion.php';</script>"; //Redirige a gestion.php
            exit;
        } else { //Nos quedamos en la página
            echo "<script>alert('Error al borrar la mascota'); 
            window.location.href='".$_SERVER['PHP_SELF']."';</script>";
            exit;
        }
    } else { //En caso de no tener permiso de borrar esa mascota
        echo "<script>alert('No tienes permiso para borrar esta mascota'); window.location.href='".$_SERVER['PHP_SELF']."';</script>";
        exit;
    }
}
//Lógica de borrar reserva de la tabla
if (isset($_GET['borrarReserva']) && is_numeric($_GET['borrarReserva'])) {
    $idReservaABorrar = intval($_GET['borrarReserva']); //Recogemos el id a borrar como int
    //Buscamos si existe
    $sqlReserva = "SELECT * FROM reservas WHERE idReserva = $idReservaABorrar AND idUsuario = $usuario";
    $resReserva = $conexion->query($sqlReserva);
    if ($resReserva && $resReserva->num_rows > 0) {
        //Hacemos el delete de la reserva
        $sqlDeleteReserva = "DELETE FROM reservas WHERE idReserva = $idReservaABorrar";
        if ($conexion->query($sqlDeleteReserva)) {
            echo "<script>alert('Reserva borrada con éxito'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "?idUsuario=$idUsuario';</script>";
            exit;
        } else {
            echo "<script>alert('Error al borrar la reserva'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar esta reserva'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }
}
//Select para mostrar las reservas en tabla
$sqlReserva = "SELECT * FROM reservas WHERE idMascota = $idMascota ORDER BY fecha DESC";
$resultadoReserva = $conexion->query($sqlReserva);
?>
<main class="contenido-principal">
<main class="container">
    <section class="row">
        <section class="col">
        
        </section>
        <section class="col-10">
                <!--Nombre de mascota-->
                <h1 class="hIzquierda"><?php echo $nombre ?>
                <!--Enlace a formulario de modificar esa mascota-->
                <a href="modificarMascota.php?idMascota=<?php echo $idMascota; ?>"><i class="bi bi-pencil"></i></a>
                <!--Enlace a borrar esa mascota-->
                <a href="?idMascota=<?php echo $idMascota; ?>&borrar=<?php echo $idMascota; ?>"
                    onclick="return confirm('¿Seguro que quieres borrar esta mascota?');">
                    <i class="bi bi-trash"></i></a>
                <!--Enlace a perfil de usuario del dueño de esa mascota-->
                <h2 class="hIzquierda">Dueño: <a href="detallesUsuarios.php?idUsuario=<?php echo $idUsuario; ?>"><?php echo $nombreUsuario. " ". $apellidosUsuario ?></a></h2>
                <h2 class="hIzquierda">Tipo: <?php echo $tipo ?></h2>
                <h2 class="hIzquierda">Raza: <?php echo $raza ?></h2>
                <h2 class="hIzquierda">Fecha de nacimiento: <?php echo $nacimiento ?></h2>
                <h2 class="hIzquierda">Historial: <?php echo $historial ?></h2>
                <h2 class="hIzquierda">Citas:</h2>
                <!--Tabla de citas de esa mascota-->
                    <?php if ($resultadoReserva && $resultadoReserva->num_rows > 0) { ?>
                        <table class="table" id="tablaCitas">
                            <thead>
                                <tr>
                                    <th scope="col">Servicio</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Hora</th>
                                    <th scope="col">+ info</th>
                                    <th scope="col">Modificar</th>
                                    <th scope="col">Borrar</th>
                                </tr>
                            </thead>
                            <tbody>
                            <!--Lógica tabla de citas-->
                            <?php while($row = $resultadoReserva->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['servicio']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hora']); ?></td>
                                    <!--Enlace a detalles de esa reserva-->
                                    <td><a href="detallesReserva.php?idReserva=<?php echo $row['idReserva']; ?>">+ info</a></td>
                                    <!--Enlace a formulario de modificar esa reserva-->
                                    <td><a href="modificarReserva.php?idReserva=<?php echo $row['idReserva']; ?>"><i class="bi bi-pencil"></i></a></td>
                                    <!--Enlace a borrar esa reserva-->
                                    <td><a href="?idMascota=<?php echo $idMascota; ?>&borrarReserva=<?php echo $row['idReserva']; ?>&acordeon=reservas"
                                    onclick="return confirm('¿Seguro que quieres borrar esta reserva?');">
                                    <i class="bi bi-trash"></i></a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
        </section>
        <section class="col">
        </section>
    </section>
    </main>
</main>  
<?php
include_once 'footer.php';
?>