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

if (isset($_GET['idUsuario'])) {//Recoge el idMascota de la URL
    $idUsuario = intval($_GET['idUsuario']); //Convierte a entero por seguridad
} else {
    echo "<script>alert('Usuario no especificado'); window.location = 'gestion.php';</script>";
    exit;
}
//Select para mostrar el usuario con ese id
$sqlUsuario = "SELECT * FROM usuarios WHERE idUsuario = $idUsuario";
$resultadoUsuario = $conexion->query($sqlUsuario);
if ($resultadoUsuario && $resultadoUsuario->num_rows > 0) {
    $usuario = $resultadoUsuario->fetch_assoc();
    //Los resultados de la select
    $email = $usuario['email'];
    $nombreUsuario = $usuario['nombreUsuario'];
    $apellidosUsuario = $usuario['apellidosUsuario'];
    $telefono = $usuario['telefono'];
    $DNIUsuario = $usuario['DNI'];
    $rol = $usuario['rol'];
} else {
    echo "<p>Usuario no encontrado.</p>";
}
//Lógica de borrar usuario
if (isset($_GET['borrar']) && is_numeric($_GET['borrar'])) {
    $idUsuarioABorrar = intval($_GET['borrar']);//Recogemos el id a borrar como int
    //Buscamos si existe
    $sqlCheck = "SELECT * FROM usuarios WHERE idUsuario = $idUsuarioABorrar";
    $resCheck = $conexion->query($sqlCheck);
    if ($resCheck && $resCheck->num_rows > 0) {
        //Buscamos todas las mascotas del usuario
        $sqlMascotasUsuario = "SELECT idMascota FROM mascotas WHERE idUsuario = $idUsuarioABorrar";
        $resMascotas = $conexion->query($sqlMascotasUsuario);
        if ($resMascotas && $resMascotas->num_rows > 0) {
            while ($rowMascota = $resMascotas->fetch_assoc()) {
                $idMascota = $rowMascota['idMascota'];
                //Hacemos el delete de reservas de esa mascota
                $sqlDeleteReservas = "DELETE FROM reservas WHERE idMascota = $idMascota";
                $conexion->query($sqlDeleteReservas);
            }
        }
        //Hacemos el delete de mascotas de ese usuario
        $sqlDeleteMascotas = "DELETE FROM mascotas WHERE idUsuario = $idUsuarioABorrar";
        $conexion->query($sqlDeleteMascotas);
        //Borrar el usuario
        $sqlDeleteUsuario = "DELETE FROM usuarios WHERE idUsuario = $idUsuarioABorrar";
        if ($conexion->query($sqlDeleteUsuario)) {
            echo "<script>alert('Usuario borrado con éxito'); 
            window.location.href='gestion.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error al borrar el usuario'); 
            window.location.href='gestion.php?acordeon=usuarios';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar este usuario'); 
        window.location.href='gestion.php?acordeon=usuarios';</script>";
        exit();
    }
}
//Lógica de borrar mascota
if (isset($_GET['borrarMascota']) && is_numeric($_GET['borrarMascota'])) {
    $idMascotaABorrar = intval($_GET['borrarMascota']);
    //Buscamos si existe
    $stmtCheck = $conexion->prepare("SELECT idMascota FROM mascotas WHERE idMascota = ?");
    $stmtCheck->bind_param("i", $idMascotaABorrar);
    $stmtCheck->execute();
    $resultado = $stmtCheck->get_result();
    if ($resultado && $resultado->num_rows > 0) {
        //Hacemos el delete de reservas de esa mascota
        $stmtDeleteReservas = $conexion->prepare("DELETE FROM reservas WHERE idMascota = ?");
        $stmtDeleteReservas->bind_param("i", $idMascotaABorrar);
        $stmtDeleteReservas->execute();
        //Hacemos el delete de esa mascota
        $stmtDeleteMascota = $conexion->prepare("DELETE FROM mascotas WHERE idMascota = ?");
        $stmtDeleteMascota->bind_param("i", $idMascotaABorrar);
        if ($stmtDeleteMascota->execute()) {
            echo "<script>alert('Mascota borrada con éxito'); 
            window.location.href='".$_SERVER['PHP_SELF']."?idUsuario=".$_GET['idUsuario']."&acordeon=mascotas';</script>";
            exit();
        } else {
            echo "<script>alert('Error al borrar la mascota'); 
            window.location.href='".$_SERVER['PHP_SELF']."?acordeon=mascotas';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar esta mascota'); 
        window.location.href='".$_SERVER['PHP_SELF']."?acordeon=mascotas';</script>";
        exit();
    }
}
//Lógica de borrar reserva de la tabla
if (isset($_GET['borrarReserva']) && is_numeric($_GET['borrarReserva'])) {
    $idReservaABorrar = intval($_GET['borrarReserva']);
    //Buscamos si existe
    $sqlReserva = "SELECT * FROM reservas WHERE idReserva = $idReservaABorrar AND idUsuario = $idUsuario";
    $resReserva = $conexion->query($sqlReserva);
    if ($resReserva && $resReserva->num_rows > 0) {
        //Hacemos el delete de la reserva
        $sqlDeleteReserva1 = "DELETE FROM reservas WHERE idReserva = $idReservaABorrar";
        if ($conexion->query($sqlDeleteReserva1)) {
            echo "<script>alert('Reserva borrada con éxito'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "?idUsuario=$idUsuario';</script>";
            exit();
        } else {
            echo "<script>alert('Error al borrar la reserva'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar esta reserva'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit();
    }
}
//Select para mostrar las mascotas en tabla
$sqlMascota = "SELECT * FROM mascotas WHERE idUsuario = $idUsuario";
$resultadoMascota = $conexion->query($sqlMascota);
//Select para mostrar las reservas en tabla
$sqlReserva = "SELECT * FROM reservas WHERE idUsuario = $idUsuario ORDER BY fecha DESC";
$resultadoReserva = $conexion->query($sqlReserva);
?>
<main class="contenido-principal">
<main class="container">
    <section class="row">
    <section class="col">
    </section>
    <section class="col-10">
        <h1 class="hIzquierda">
        <!--Enlace a formulario de modificar ese usuario-->
        <a href="modificarUsuario.php?idUsuario=<?php echo $idUsuario; ?>"><i class="bi bi-pencil"></i></a>
        <!--Enlace a borrar ese usuario-->
        <a href="?idUsuario=<?php echo $idUsuario; ?>&borrar=<?php echo $idUsuario; ?>"
            onclick="return confirm('¿Seguro que quieres borrar este usuario?');">
            <i class="bi bi-trash"></i></a>
        <h2 class="hIzquierda">Nombre: <?php echo $nombreUsuario ?></h2>
        <h2 class="hIzquierda">Apellidos: <?php echo $apellidosUsuario ?></h2>
        <h2 class="hIzquierda">DNI: <?php echo $DNIUsuario ?></h2>
        <h2 class="hIzquierda">Email: <?php echo $email ?></h2>
        <h2 class="hIzquierda">Teléfono: <?php echo $telefono ?></h2>
        <h2 class="hIzquierda">Rol: <?php echo $rol ?></h2>
        <h2 class="hIzquierda">Mascotas:</h2>
        <!--Tabla de mascotas de ese usuario-->
        <?php if ($resultadoMascota && $resultadoMascota->num_rows > 0) { ?>
            <table class="table" id="tablaMisMascotas">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Raza</th>
                        <th scope="col">Fecha de nacimiento</th>
                        <th scope="col">+ info</th>
                        <th scope="col">Modificar</th>
                        <th scope="col">Borrar</th>
                    </tr>
                </thead>
                <tbody>
                <?php $contador = 1; while($row = $resultadoMascota->fetch_assoc()) { ?>
                    <tr>
                        <th scope="row"><?php echo $contador++; ?></th>
                        <td><?php echo htmlspecialchars($row['nombreMascota']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipoAnimal']); ?></td>
                        <td><?php echo htmlspecialchars($row['raza']); ?></td>
                        <td><?php echo htmlspecialchars($row['fechaNacimiento']); ?></td>
                        <!--Enlace a detalles de esa mascota-->
                        <td><a href="detallesMascota.php?idMascota=<?php echo $row['idMascota']; ?>">+ info</a></td>
                        <!--Enlace a formulario de modificar esa mascota-->
                        <td><a href="modificarMascota.php?idMascota=<?php echo $row['idMascota']; ?>"><i class="bi bi-pencil"></i></a></td>
                        <!--Enlace a borrar esa mascota-->
                        <td><a href="?idUsuario=<?php echo $idUsuario; ?>&borrarMascota=<?php echo $row['idMascota']; ?>&acordeon=mascotas"
                        onclick="return confirm('¿Seguro que quieres borrar esta mascota?');">
                        <i class="bi bi-trash"></i></a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
        <h2 class="hIzquierda">Citas:</h2>
        <!--Tabla de citas de ese usuario-->
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
                <?php $contador = 1; while($row = $resultadoReserva->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['servicio']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($row['hora']); ?></td>
                        <!--Enlace a detalles de esa reserva-->
                        <td><a href="detallesReserva.php?idReserva=<?php echo $row['idReserva']; ?>">+ info</a></td>
                        <!--Enlace a formulario de modificar esa reserva-->
                        <td><a href="modificarReserva.php?idReserva=<?php echo $row['idReserva']; ?>"><i class="bi bi-pencil"></i></a></td>
                        <!--Enlace a borrar esa reserva-->
                        <td><a href="?idUsuario=<?php echo $idUsuario; ?>&borrarReserva=<?php echo $row['idReserva']; ?>&acordeon=reservas"
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
<?php include_once 'footer.php'; ?>
