<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';

if (isset($_GET['idMascota'])) {
    $idMascota = intval($_GET['idMascota']); // Convierte a entero por seguridad
} else {
    // Redirigir o mostrar error si no llega el parámetro
    echo "<script>alert('Mascota no especificada'); window.location = 'miPerfil.php';</script>";
    exit;
}

$sql = "SELECT * FROM mascotas WHERE idMascota = $idMascota";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $mascota = $resultado->fetch_assoc();
    $nombre= $mascota['nombreMascota'];
    $tipo= $mascota['tipoAnimal'];
    $raza= $mascota['raza'];
    $nacimiento= $mascota['fechaNacimiento'];
    $historial= $mascota['Historial'];
    // Ahora puedes usar $mascota['nombreMascota'], $mascota['tipoAnimal'], etc.
} else {
    echo "<p>Mascota no encontrada.</p>";
}


?>
<main class="contenido-principal">
<main class="container">
    <section class="row">
    <section class="col">
      
    </section>
    <section class="col-10">
            
            <h1 class="hIzquierda"><?php echo $nombre ?>
            <a href="modificarMiMascota.php?idMascota=<?php echo $idMascota; ?>"><i class="bi bi-pencil"></i></a>
            <a href="?borrar=<?php echo $idMascota; ?>"
                onclick="return confirm('¿Seguro que quieres borrar esta mascota?');">
                <i class="bi bi-trash"></i></a></h1>
            <h2 class="hIzquierda">Tipo: <?php echo $tipo ?></h2>
            <h2 class="hIzquierda">Raza: <?php echo $raza ?></h2>
            <h2 class="hIzquierda">Fecha de nacimiento: <?php echo $nacimiento ?></h2>
            <h2 class="hIzquierda">Historial: <?php $historial ?></h2>
        
        </section>
        <section class="col">
   
   </section>
   </section>
   </main>




</main>  
<?php
include_once 'footer.php';
?>