<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';

// Redirige si no hay sesión activa
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit;
  }

$idMascota = intval($_GET['idMascota']);
$sql = "SELECT * FROM mascotas WHERE idMascota = $idMascota";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    $mascotaData = $resultado->fetch_assoc();
    $nombreData= $mascotaData['nombreMascota'];
    $tipoData= $mascotaData['tipoAnimal'];
    $razaData= $mascotaData['raza'];
    $nacimientoData= $mascotaData['fechaNacimiento'];
    // Ahora puedes usar $mascota['nombreMascota'], $mascota['tipoAnimal'], etc.
}

if (isset($_POST['submitModMiMascota'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombreModMiMascota']);
    $tipo = mysqli_real_escape_string($conexion, $_POST['tipoModMiMascota']);
    $raza = mysqli_real_escape_string($conexion, $_POST['razaModMiMascota']);
    $nacimiento = mysqli_real_escape_string($conexion, $_POST['nacimientoModMiMascota']);
  
    $sqlUpdate = "UPDATE mascotas 
                  SET nombreMascota='$nombre', tipoAnimal='$tipo', raza='$raza', fechaNacimiento='$nacimiento'
                  WHERE idMascota = $idMascota AND idUsuario = {$_SESSION['idUsuario']}";
  
    if ($conexion->query($sqlUpdate)) {
      echo "<script>alert('Mascota actualizada correctamente'); window.location='miPerfil.php';</script>";
    } else {
      echo "<script>alert('Error al actualizar la mascota');</script>";
    }
  } else {
    // Consulta datos actuales
    $sqlSelect = "SELECT * FROM mascotas WHERE idMascota = $idMascota AND idUsuario = {$_SESSION['idUsuario']}";
    $resultado = $conexion->query($sqlSelect);
  
    if ($resultado && $resultado->num_rows > 0) {
      $mascota = $resultado->fetch_assoc();
    } else {
      echo "<script>alert('Mascota no encontrada'); window.location='miPerfil.php';</script>";
      exit;
    }
  }



?>

<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Modifica tu mascota</h1>
    </section>
  <section class="login">

  <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <!--Nombre-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Nombre:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="nombreModMiMascota" value="<?php echo htmlspecialchars($nombreData); ?>"  required>
      </aside>
    </article>
    <!--Tipo-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Tipo:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="tipoModMiMascota" value="<?php echo htmlspecialchars($tipoData); ?>" placeholder="Perro, gato.."  required>
      </aside>
    </article>
    <!--Raza-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Raza:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="razaModMiMascota" value="<?php echo htmlspecialchars($razaData); ?>"  required>
      </aside>
    </article>
    <!--Fecha de nacimiento-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
      <aside class="col-sm-6">
        <input type="Date" class="form-control" name="nacimientoModMiMascota" value="<?php echo htmlspecialchars($nacimientoData); ?>"  required>
      </aside>
    </article>
    <!--Boton-->
    <article class="col-auto">
      <button type="submit" name="submitModMiMascota" class="btn btn-info mb-3" >Modificar mascota</button>
    </article>
  </form>
  </section>
</main>  

<?php
include_once 'footer.php';
?>