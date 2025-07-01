<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';


$usuario = $_SESSION['idUsuario'];
$sql = "SELECT * FROM mascotas where idUsuario = '$usuario'";
$resultado = $conexion->query($sql);
$contador = 0;

// Lógica para borrar mascota
if (isset($_GET['borrar']) && is_numeric($_GET['borrar'])) {
    $idMascotaABorrar = intval($_GET['borrar']);

    // Verificar que la mascota pertenezca al usuario (por seguridad)
    $sqlCheck = "SELECT * FROM mascotas WHERE idMascota = $idMascotaABorrar AND idUsuario = $usuario";
    $resCheck = $conexion->query($sqlCheck);

    if ($resCheck && $resCheck->num_rows > 0) {
        $sqlDelete = "DELETE FROM mascotas WHERE idMascota = $idMascotaABorrar";
        if ($conexion->query($sqlDelete)) {
            echo "<script>alert('Mascota borrada con éxito'); 
            window.location.href='".$_SERVER['PHP_SELF']."';</script>";
            exit;
        } else {
            echo "<script>alert('Error al borrar la mascota'); 
            window.location.href='".$_SERVER['PHP_SELF']."';</script>";
            exit;
        }
    } else {
        echo "<script>alert('No tienes permiso para borrar esta mascota'); window.location.href='".$_SERVER['PHP_SELF']."';</script>";
        exit;
    }
}
?>
<main class="contenido-principal container">

    <main class="container">
    <section class="row">
    <section class="col">
      
    </section>
    <section class="col-10">

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
                        <p>Nombre: <?php echo $_SESSION['nombreUsuario']?></p>
                        <p>Apellidos: <?php echo $_SESSION['apellidosUsuario']?></p>
                        <p>Teléfono: <?php echo $_SESSION['telefono']?></p>
                        <p>DNI: <?php echo $_SESSION['DNI']?></p>
                        <p><a href="editarMiPerfil.php">Editar perfil</a></p>
                    </aside>                
                </aside>
            </article>
            <article class="accordion-item" id="mascotasPerfil">
                <h2 class="accordion-header" id="flush-headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    Mis mascotas
                </button>
                </h2>
                <aside id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                    <aside class="accordion-body">
                        <p><a href="crearMiMascota.php">Crear nueva mascota</a></p>
                        <?php if ($resultado && $resultado->num_rows > 0) { ?>
                        <table class="table">
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
                            <?php while($row = $resultado->fetch_assoc()) { 
                                $contador++;?>
                                <tr>
                                    <th scope="row"><?php echo $contador?></th>
                                    <td><?php echo htmlspecialchars($row['nombreMascota']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tipoAnimal']); ?></td>
                                    <td><?php echo htmlspecialchars($row['raza']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fechaNacimiento']); ?></td>
                                    <td><a href="detallesMascota.php?idMascota=<?php echo $row['idMascota']; ?>">+ info</a></td>
                                    <td><a href="modificarMiMascota.php?idMascota=<?php echo $row['idMascota']; ?>"><i class="bi bi-pencil"></i></a></td>
                                    <td><a href="?borrar=<?php echo $row['idMascota'] ?>"
                                        onclick="return confirm('¿Seguro que quieres borrar esta mascota?');">
                                        <i class="bi bi-trash"></i></a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php } ?>
                            
                    </aside>
                </aside>
            </article>
            <article class="accordion-item" id="reservasPerfil">
                <h2 class="accordion-header" id="flush-headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                    Mis reservas
                </button>
                </h2>
                <aside id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                    <aside class="accordion-body">
                        
                    </aside>                
                </aside>
            </article>
        </section>
        <p style="text-align: right;"><a href="cerrarSesion.php">Cerrar sesion</a></>
    </section>
    <section class="col">
   
    </section>
    </section>
    </main>


</main>  

<?php
include_once 'footer.php';
?>