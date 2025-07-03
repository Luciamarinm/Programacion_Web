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
//Variable de sesión para identificar el usuario de la sesión
$usuario = $_SESSION['idUsuario'];
//Lógica de borrar mascota
if (isset($_GET['borrar']) && is_numeric($_GET['borrar'])) {
    $idMascotaABorrar = intval($_GET['borrar']);
    //Buscamos si existe
    $sqlCheck = "SELECT * FROM mascotas WHERE idMascota = $idMascotaABorrar AND idUsuario = $usuario";
    $resCheck = $conexion->query($sqlCheck);
    if ($resCheck && $resCheck->num_rows > 0) {
        //Hacemos el delete de reservas de esa mascota
        $sqlDeleteReservas = "DELETE FROM reservas WHERE idMascota = $idMascotaABorrar";
            $conexion->query($sqlDeleteReservas);
        //Hacemos el delete de esa mascota
        $sqlDelete = "DELETE FROM mascotas WHERE idMascota = $idMascotaABorrar";
        if ($conexion->query($sqlDelete)) {
            echo "<script>alert('Mascota borrada con éxito'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "?acordeon=mascotas';</script>";
            exit;
        } else {
            echo "<script>alert('Error al borrar la mascota'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "?acordeon=mascotas';</script>";
            exit;
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar esta mascota'); window.location.href='" . $_SERVER['PHP_SELF'] . "?acordeon=mascotas';</script>";
        exit;
    }
}
//Lógica de borrar reserva de la tabla
if (isset($_GET['borrarReserva']) && is_numeric($_GET['borrarReserva'])) {
    $idReservaABorrar = intval($_GET['borrarReserva']);
    //Buscamos si existe
    $sqlCheck = "SELECT * FROM reservas WHERE idReserva = $idReservaABorrar AND idUsuario = $usuario";
    $resCheck = $conexion->query($sqlCheck);
    if ($resCheck && $resCheck->num_rows > 0) {
        //Hacemos el delete de la reserva
        $sqlDelete = "DELETE FROM reservas WHERE idReserva = $idReservaABorrar";
        if ($conexion->query($sqlDelete)) {
            echo "<script>alert('Reserva borrada con éxito'); 
            window.location.href='".$_SERVER['PHP_SELF']."?acordeon=reservas';</script>";
            exit;
        } else {
            echo "<script>alert('Error al borrar la reserva'); 
            window.location.href='".$_SERVER['PHP_SELF']."?acordeon=reservas';</script>";
            exit;
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar esta reserva'); window.location.href='".$_SERVER['PHP_SELF']."?acordeon=reservas';</script>";
        exit;
    }
}
//Select para mostrar el usuario con ese id
$sqlUsuario = "SELECT * FROM usuarios WHERE idUsuario = $usuario";
$resultadoUsuario = $conexion->query($sqlUsuario);
if ($resultadoUsuario && $resultadoUsuario->num_rows > 0) {
    $usuarioSel = $resultadoUsuario->fetch_assoc();
    //Los resultados de la select
    $email = $usuarioSel['email'];
    $nombreUsuario = $usuarioSel['nombreUsuario'];
    $apellidosUsuario = $usuarioSel['apellidosUsuario'];
    $telefono = $usuarioSel['telefono'];
    $DNIUsuario = $usuarioSel['DNI'];
    $rol = $usuarioSel['rol'];
} else {
    echo "<p>Usuario no encontrado.</p>";
}
//Consultas para mostrar datos
$sql = "SELECT * FROM mascotas WHERE idUsuario = $usuario";
$resultado = $conexion->query($sql);
$sqlReservas = "SELECT * FROM reservas WHERE idUsuario = $usuario  ORDER BY fecha DESC";
$resultadoReserva = $conexion->query($sqlReservas);
//Estado de acordeón
$acordeon = isset($_GET['acordeon']) ? $_GET['acordeon'] : '';
?>
<main class="contenido-principal container">
    <main class="container">
        <section class="row">
            <section class="col">
            </section>
            <section class="col-10">
                <!-- Usuarios -->
                <h1 class="hInicio">Mi perfil</h1>
                <section class="accordion accordion-flush" id="accordionFlushExample">
                    <article class="accordion-item" id="usuarioPerfil">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                Mi usuario
                            </button>
                        </h2>
                        <aside id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <aside class="accordion-body">
                                <!-- Datos del usuario -->
                                <p>Nombre: <?php echo $nombreUsuario ?></p>
                                <p>Apellidos: <?php echo $apellidosUsuario ?></p>
                                <p>Teléfono: <?php echo $telefono ?></p>
                                <p>DNI: <?php echo $DNIUsuario ?></p>
                                <!-- Enlace a formulario de modificar el usuario -->
                                <p><a href="modificarMiUsuario.php?idUsuario=<?php echo $usuario ?>">Editar mi perfil</a></p>
                            </aside>
                        </aside>
                    </article>
                    <!-- Mascotas -->
                    <article class="accordion-item" id="mascotasPerfil">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button <?php echo ($acordeon === 'mascotas') ? '' : 'collapsed'; ?>"
                                type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                                aria-expanded="<?php echo ($acordeon === 'mascotas') ? 'true' : 'false'; ?>"
                                aria-controls="flush-collapseTwo">
                                Mis mascotas
                            </button>
                        </h2>
                        <aside id="flush-collapseTwo" class="accordion-collapse collapse <?php echo ($acordeon === 'mascotas') ? 'show' : ''; ?>"
                            aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                            <aside class="accordion-body">
                                <p><a href="crearMiMascota.php">Crear nueva mascota</a></p>
                                <aside class="mb-3">
                                    <!-- Buscador de la tabla: buscadorMisMascotas.js -->
                                    <input type="text" id="buscadorMisMascotas" class="form-control" placeholder="Buscar mascota...">
                                </aside>
                                <!-- Tabla de mascotas -->
                                <?php if ($resultado && $resultado->num_rows > 0) { ?>
                                    <table class="table" id="tablaMisMascotas">
                                        <thead>
                                            <tr>
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
                                            <?php while ($row = $resultado->fetch_assoc()) { ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nombreMascota']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['tipoAnimal']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['raza']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['fechaNacimiento']); ?></td>
                                                    <!-- Enlace a detalles de mascota -->
                                                    <td><a href="detallesMiMascota.php?idMascota=<?php echo $row['idMascota']; ?>">+ info</a></td>
                                                    <!-- Enlace a formulario de modificar mascota -->
                                                    <td><a href="modificarMiMascota.php?idMascota=<?php echo $row['idMascota']; ?>"><i class="bi bi-pencil"></i></a></td>
                                                    <!-- Enlace a borrar la mascota -->
                                                    <td><a href="?borrar=<?php echo $row['idMascota'] ?>&acordeon=mascotas"
                                                            onclick="return confirm('¿Seguro que quieres borrar esta mascota?');">
                                                            <i class="bi bi-trash"></i></a></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <p>No tienes mascotas registradas.</p>
                                <?php } ?>
                            </aside>
                        </aside>
                    </article>
                    <!-- Reservas -->
                    <article class="accordion-item" id="reservasPerfil">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button <?php echo ($acordeon === 'reservas') ? '' : 'collapsed'; ?>" 
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseThree"
                                aria-expanded="<?php echo ($acordeon === 'reservas') ? 'true' : 'false'; ?>"
                                aria-controls="flush-collapseThree">
                                Reservas
                            </button>
                        </h2>
                        <aside id="flush-collapseThree" class="accordion-collapse collapse <?php echo ($acordeon === 'reservas') ? 'show' : ''; ?>"
                            aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                            <aside class="accordion-body">
                                <p><a href="crearMiReserva.php">Crear nueva reserva</a></p>
                                <aside class="mb-3">
                                    <!-- Buscador de la tabla: buscadorMisReservas.js -->
                                    <input type="text" id="buscadorMisReservas" class="form-control"
                                        placeholder="Buscar reserva...">
                                </aside>
                                <?php if ($resultadoReserva && $resultadoReserva->num_rows > 0) {?>
                                <!-- Tabla de reservas -->
                                <table class="table" id="tablaMisReservas">
                                    <thead>
                                        <tr>
                                            <th scope="col">Servicio</th>
                                            <th scope="col">Fecha</th>
                                            <th scope="col">Hora</th>
                                            <th scope="col">Mascota</th>
                                            <th scope="col">+ info</th>
                                            <th scope="col">Modificar</th>
                                            <th scope="col">Borrar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $resultadoReserva->fetch_assoc()) {?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['servicio']); ?></td>
                                            <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                            <td><?php echo htmlspecialchars($row['hora']); ?></td>
                                            <?php //Lógica de mostrar datos de las mascotas en la tabla de reservas
                                            $idOwnerReserva = $row['idUsuario'];
                                            $idPet = $row['idMascota'];
                                            $sqlPetReserva = "SELECT nombreMascota, tipoAnimal FROM mascotas WHERE idMascota = $idPet";
                                            $resPetReserva = $conexion->query($sqlPetReserva);
                                            if ($resPetReserva && $resPetReserva->num_rows > 0) {
                                                $mascotaReserva = $resPetReserva->fetch_assoc();
                                                $nombreMascotaReserva = $mascotaReserva['nombreMascota'];
                                                $tipoMascotaReserva = $mascotaReserva['tipoAnimal'];
                                            } else {
                                                $nombreMascotaReserva = 'No encontrada';
                                                $tipoMascotaReserva = '';
                                            }
                                            ?>
                                            <td><?php echo $nombreMascotaReserva; ?></td>
                                            <!-- Enlace de detalles de la reserva -->
                                            <td><a href="detallesMiReserva.php?idReserva=<?php echo $row['idReserva']; ?>">+ info</a></td>
                                            <!-- Enlace al formulario de modificar la reserva -->
                                            <td><a href="modificarMiReserva.php?idReserva=<?php echo $row['idReserva']; ?>"><i class="bi bi-pencil"></i></a></td>
                                            <!-- Enlace a borrar la reserva -->
                                            <td><a href="?borrarReserva=<?php echo $row['idReserva']; ?>&acordeon=reservas"
                                                onclick="return confirm('¿Seguro que quieres borrar esta reserva?');"><i class="bi bi-trash"></i></a></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <?php } ?>
                            </aside>
                        </aside>
                    </article>
                </section>
                <p style="text-align: right;"><a href="cerrarSesion.php">Cerrar sesion</a></p>
            </section>
            <section class="col">
            </section>
        </section>
    </main>
</main>
<?php
include_once 'footer.php';
?>
