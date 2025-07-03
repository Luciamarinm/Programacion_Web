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
//Parámetro para mantener abierto el acordeón
$acordeon = isset($_GET['acordeon']) ? $_GET['acordeon'] : '';
//Consultas para mostrar datos
$sqlUsuarios = "SELECT * FROM usuarios";
$resultadoUsuario = $conexion->query($sqlUsuarios);
$sqlMascotas = "SELECT * FROM mascotas";
$resultadoMascota = $conexion->query($sqlMascotas);
$sqlReservas = "SELECT * FROM reservas ORDER BY fecha DESC";
$resultadoReserva = $conexion->query($sqlReservas);
//Lógica de borrar usuario
if (isset($_GET['borrarUsuario']) && is_numeric($_GET['borrarUsuario'])) {
    $idUsuarioABorrar = intval($_GET['borrarUsuario']);//Recogemos el id a borrar como int
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
        //Hacemos el delete de reservas de esa mascota
        $sqlDeleteMascotas = "DELETE FROM mascotas WHERE idUsuario = $idUsuarioABorrar";
        $conexion->query($sqlDeleteMascotas);
        //Borrar el usuario
        $sqlDeleteUsuario = "DELETE FROM usuarios WHERE idUsuario = $idUsuarioABorrar";
        if ($conexion->query($sqlDeleteUsuario)) {
            echo "<script>alert('Usuario borrado con éxito'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "?acordeon=usuarios';</script>";
            exit;
        } else {
            echo "<script>alert('Error al borrar el usuario'); 
            window.location.href='" . $_SERVER['PHP_SELF'] . "?acordeon=usuarios';</script>";
            exit;
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar este usuario'); 
        window.location.href='" . $_SERVER['PHP_SELF'] . "?acordeon=usuarios';</script>";
        exit;
    }
}
//Lógica de borrar mascota
if (isset($_GET['borrarMascota']) && is_numeric($_GET['borrarMascota'])) {
    $idMascotaABorrar = intval($_GET['borrarMascota']);
    //Buscamos si existe
    $sqlCheck = "SELECT * FROM mascotas WHERE idMascota = $idMascotaABorrar";
    $resCheck = $conexion->query($sqlCheck);
    if ($resCheck && $resCheck->num_rows > 0) {
        //Hacemos el delete de reservas de esa mascota
        $sqlDeleteReservas2 = "DELETE FROM reservas WHERE idMascota = $idMascotaABorrar";
            $conexion->query($sqlDeleteReservas2);
        //Hacemos el delete de esa mascota
        $sqlDeleteMascota1 = "DELETE FROM mascotas WHERE idMascota = $idMascotaABorrar";
        if ($conexion->query($sqlDeleteMascota1)) {
            echo "<script>alert('Mascota borrada con éxito'); 
            window.location.href='".$_SERVER['PHP_SELF']."?acordeon=mascotas';</script>";
            exit;
        } else {
            echo "<script>alert('Error al borrar la mascota'); 
            window.location.href='".$_SERVER['PHP_SELF']."?acordeon=mascotas';</script>";
            exit;
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar esta mascota'); window.location.href='".$_SERVER['PHP_SELF']."?acordeon=mascotas';</script>";
        exit;
    }
}
//Lógica de borrar reserva de la tabla
if (isset($_GET['borrarReserva']) && is_numeric($_GET['borrarReserva'])) {
    $idReservaABorrar = intval($_GET['borrarReserva']);
    //Buscamos si existe
    $sqlCheck = "SELECT * FROM reservas WHERE idReserva = $idReservaABorrar";
    $resCheck = $conexion->query($sqlCheck);
    if ($resCheck && $resCheck->num_rows > 0) {
        //Hacemos el delete de la reserva
        $sqlDeleteReserva1 = "DELETE FROM reservas WHERE idReserva = $idReservaABorrar";
        if ($conexion->query($sqlDeleteReserva1)) {
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
?>
<main class="contenido-principal container">
    <main class="container">
        <section class="row">
            <section class="col"></section>
            <section class="col-10">
                <h1 class="hInicio">GESTIÓN</h1>
                <section class="accordion accordion-flush" id="accordionFlushExample">
                    <!-- Usuarios -->
                    <article class="accordion-item" id="usuariosPerfil">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button
                            class="accordion-button <?php echo ($acordeon === 'usuarios') ? '' : 'collapsed'; ?>"
                            type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                            aria-expanded="<?php echo ($acordeon === 'usuarios') ? 'true' : 'false'; ?>"
                            aria-controls="flush-collapseOne">
                            Usuarios
                            </button>
                        </h2>
                        <aside id="flush-collapseOne"
                            class="accordion-collapse collapse <?php echo ($acordeon === 'usuarios') ? 'show' : ''; ?>"
                            aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                            <p><a href="crearNuevoUsuario.php">Crear nuevo usuario</a></p>
                            <div class="mb-3">
                                <!-- Buscador de la tabla: buscadorUsuarios.js -->
                                <input type="text" id="buscadorUsuarios" class="form-control" placeholder="Buscar usuario...">
                            </div>
                            <!-- Tabla de usuarios -->
                            <?php if ($resultadoUsuario && $resultadoUsuario->num_rows > 0) { ?>
                                <table class="table" id="tablaUsuarios">
                                <thead>
                                    <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Apellidos</th>
                                    <th scope="col">Telefono</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">DNI</th>
                                    <th scope="col">Rol</th>
                                    <th scope="col">+ info</th>
                                    <th scope="col">Modificar</th>
                                    <th scope="col">Borrar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $resultadoUsuario->fetch_assoc()) { ?>
                                    <tr>
                                        <th scope="row"><?php echo htmlspecialchars($row['idUsuario']); ?></th>
                                        <td><?php echo htmlspecialchars($row['nombreUsuario']); ?></td>
                                        <td><?php echo htmlspecialchars($row['apellidosUsuario']); ?></td>
                                        <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['DNI']); ?></td>
                                        <td><?php echo htmlspecialchars($row['rol']); ?></td>
                                        <!-- Enlace a detalles de usuario -->
                                        <td><a href="detallesUsuarios.php?idUsuario=<?php echo $row['idUsuario']; ?>">+ info</a></td>
                                        <!-- Enlace a formulario de modificar usuario -->
                                        <td><a href="modificarUsuario.php?idUsuario=<?php echo $row['idUsuario']; ?>"><i class="bi bi-pencil"></i></a></td>
                                        <!-- Enlace para borrar usuario -->
                                        <td><a href="?borrarUsuario=<?php echo $row['idUsuario'] ?>&acordeon=usuarios"
                                            onclick="return confirm('¿Seguro que quieres borrar este usuario?');"><i class="bi bi-trash"></i></a></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                                </table>
                            <?php } ?>
                            </div>
                        </aside>
                        </article>
                    <!-- Mascotas -->
                    <article class="accordion-item" id="mascotasPerfil">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button
                                class="accordion-button <?php echo ($acordeon === 'mascotas') ? '' : 'collapsed'; ?>"
                                type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                                aria-expanded="<?php echo ($acordeon === 'mascotas') ? 'true' : 'false'; ?>"
                                aria-controls="flush-collapseTwo">
                                Mascotas
                            </button>
                        </h2>
                        <aside id="flush-collapseTwo"
                            class="accordion-collapse collapse <?php echo ($acordeon === 'mascotas') ? 'show' : ''; ?>"
                            aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                            <aside class="accordion-body">
                                <p><a href="crearNuevaMascota.php">Crear nueva mascota</a></p>
                                <aside class="mb-3">
                                    <!-- Buscador de la tabla: buscadorMascotas.js -->
                                    <input type="text" id="buscadorMascotas" class="form-control"
                                        placeholder="Buscar mascota...">
                                </aside>
                                <?php
                                if ($resultadoMascota && $resultadoMascota->num_rows > 0) {
                                ?>
                                <!-- Tabla de mascotas -->
                                <table class="table" id="tablaMascotas">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Raza</th>
                                            <th scope="col">Fecha de nacimiento</th>
                                            <th scope="col">Dueño</th>
                                            <th scope="col">+ info</th>
                                            <th scope="col">Modificar</th>
                                            <th scope="col">Borrar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $resultadoMascota->fetch_assoc()) {
                                            ?>
                                        <tr>
                                            <th scope="row"><?php echo htmlspecialchars($row['idMascota']); ?></th>
                                            <td><?php echo htmlspecialchars($row['nombreMascota']); ?></td>
                                            <td><?php echo htmlspecialchars($row['tipoAnimal']); ?></td>
                                            <td><?php echo htmlspecialchars($row['raza']); ?></td>
                                            <td><?php echo htmlspecialchars($row['fechaNacimiento']); ?></td>
                                            <?php //Lógica de mostrar datos del usuario en la tabla de mascotas
                                                $idOwner = $row['idUsuario'];
                                                $sqlOwner = "SELECT nombreUsuario, apellidosUsuario FROM usuarios WHERE idUsuario = $idOwner";
                                                $resOwner = $conexion->query($sqlOwner);
                                                if ($resOwner && $resOwner->num_rows > 0) {
                                                    $usuario = $resOwner->fetch_assoc();
                                                    $nombreUsuario = $usuario['nombreUsuario'];
                                                    $apellidosUsuario = $usuario['apellidosUsuario'];
                                                } else {
                                                    $nombreUsuario = 'No encontrado';
                                                    $apellidosUsuario = '';
                                                }
                                            ?>
                                            <td><?php echo $nombreUsuario . " " . $apellidosUsuario; ?></td>
                                            <!-- Enlace a detalles de mascota -->
                                            <td><a href="detallesMascota.php?idMascota=<?php echo $row['idMascota']; ?>">+ info</a></td>
                                            <!-- Enlace a formulario de modificar mascota -->
                                            <td><a href="modificarMascota.php?idMascota=<?php echo $row['idMascota']; ?>"><i class="bi bi-pencil"></i></a></td>
                                            <!-- Enlace a borrar mascota -->
                                            <td><a href="?borrarMascota=<?php echo $row['idMascota']; ?>&acordeon=mascotas"
                                                onclick="return confirm('¿Seguro que quieres borrar esta mascota?');"><i class="bi bi-trash"></i></a></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
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
                                <p><a href="crearReserva.php">Crear nueva reserva</a></p>
                                <aside class="mb-3">
                                    <!-- Buscador de la tabla: buscadorReservas.js -->
                                    <input type="text" id="buscadorReservas" class="form-control"
                                        placeholder="Buscar reserva...">
                                </aside>
                                <?php
                                if ($resultadoReserva && $resultadoReserva->num_rows > 0) {
                                ?>
                                <!-- Tabla de reservas -->
                                <table class="table" id="tablaReservas">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Servicio</th>
                                            <th scope="col">Fecha</th>
                                            <th scope="col">Hora</th>
                                            <th scope="col">Dueño</th>
                                            <th scope="col">Mascota</th>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Teléfono</th>
                                            <th scope="col">+ info</th>
                                            <th scope="col">Modificar</th>
                                            <th scope="col">Borrar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $resultadoReserva->fetch_assoc()) {
                                            ?>
                                        <tr>
                                            <th scope="row"><?php echo htmlspecialchars($row['idReserva']); ?></th>
                                            <td><?php echo htmlspecialchars($row['servicio']); ?></td>
                                            <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                            <td><?php echo htmlspecialchars($row['hora']); ?></td>
                                            <?php //Lógica de mostrar datos del usuario en la tabla de reservas
                                            $idOwnerReserva = $row['idUsuario'];
                                            $sqlOwnerReserva = "SELECT nombreUsuario, apellidosUsuario FROM usuarios WHERE idUsuario = $idOwnerReserva";
                                            $resOwnerReserva = $conexion->query($sqlOwnerReserva);
                                            if ($resOwnerReserva && $resOwnerReserva->num_rows > 0) {
                                                $usuarioReserva = $resOwnerReserva->fetch_assoc();
                                                $nombreUsuarioReserva = $usuarioReserva['nombreUsuario'];
                                                $apellidosUsuarioReserva = $usuarioReserva['apellidosUsuario'];
                                            } else {
                                                $nombreUsuarioReserva = 'No encontrado';
                                                $apellidosUsuarioReserva = '';
                                            }
                                            $idPet = $row['idMascota']; //Lógica de mostrar datos del mascotas en la tabla de reservas
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
                                            <td><?php echo $nombreUsuarioReserva . " " . $apellidosUsuarioReserva; ?></td>
                                            <td><?php echo $nombreMascotaReserva; ?></td>
                                            <td><?php echo $tipoMascotaReserva; ?></td>
                                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                                            <!-- Enlace a detalles de reservas -->
                                            <td><a href="detallesReserva.php?idReserva=<?php echo $row['idReserva']; ?>">+ info</a></td>
                                            <!-- Enlace a formulario de modificar reservas -->
                                            <td><a href="modificarReserva.php?idReserva=<?php echo $row['idReserva']; ?>"><i class="bi bi-pencil"></i></a></td>
                                            <!-- Enlace para borrar la reserva -->
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
            <section class="col"></section>
        </section>
    </main>
</main>
<?php include_once 'footer.php'; ?>
