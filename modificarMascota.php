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

if (isset($_REQUEST['idMascota'])) {//Recoge el idMascota de la URL
  $idMascota = intval($_REQUEST['idMascota']);
} else {
  echo "<script>alert('ID de mascota no especificado'); window.location='gestion.php';</script>";
  exit;
}
//Select para mostrar la mascota con ese id
$sql = "SELECT * FROM mascotas WHERE idMascota = $idMascota";
$resultado = $conexion->query($sql);
if ($resultado && $resultado->num_rows > 0) {
    $mascotaData = $resultado->fetch_assoc();
    //Los resultados de la select
    $nombreData= $mascotaData['nombreMascota'];
    $tipoData= $mascotaData['tipoAnimal'];
    $razaData= $mascotaData['raza'];
    $nacimientoData= $mascotaData['fechaNacimiento'];
    $historialData= $mascotaData['Historial'];
    $idUsuarioData= $mascotaData['idUsuario'];
}

if (isset($_POST['submitModMascota'])) {//Al clicar el botón lleva aquí
    //Valor de los inputs
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombreModMascota']);
    $tipo = mysqli_real_escape_string($conexion, $_POST['tipoModMascota']);
    $raza = mysqli_real_escape_string($conexion, $_POST['razaModMascota']);
    $nacimiento = mysqli_real_escape_string($conexion, $_POST['nacimientoModMascota']);
    $historial = mysqli_real_escape_string($conexion, $_POST['historialModMascota']);
    //Hacemos update con los datos nuevos recogidos
    $sqlUpdate = "UPDATE mascotas 
                  SET nombreMascota='$nombre', tipoAnimal='$tipo', raza='$raza', fechaNacimiento='$nacimiento', Historial='$historial'
                  WHERE idMascota = $idMascota AND idUsuario = $idUsuarioData";
    if ($conexion->query($sqlUpdate)) {
      echo "<script>alert('Mascota actualizada correctamente'); window.location='detallesMascota.php?idMascota=$idMascota';</script>";
    } else {
      echo "<script>alert('Error al actualizar la mascota');</script>";
    }
  } else {
    //Buscar datos actuales
    $sqlSelect = "SELECT * FROM mascotas WHERE idMascota = $idMascota AND idUsuario = $idUsuarioData";
    $resultado = $conexion->query($sqlSelect);
    if ($resultado && $resultado->num_rows > 0) {
      $mascota = $resultado->fetch_assoc();
    } else {
      echo "<script>alert('Mascota no encontrada'); window.location='gestion.php';</script>";
      exit;
    }
  }
?>
<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Modificar mascota</h1>
    </section>
  <section class="login">
  <!--Formulario-->
  <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <!--idMascota-->
    <input type="hidden" name="idMascota" value="<?php echo $idMascota; ?>">
    <!--Nombre-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Nombre:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="nombreModMascota" value="<?php echo htmlspecialchars($nombreData); ?>"  required>
      </aside>
    </article>
    <!--Tipo-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Tipo:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="tipoModMascota" value="<?php echo htmlspecialchars($tipoData); ?>" placeholder="Perro, gato.."  required>
      </aside>
    </article>
    <!--Raza-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Raza:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="razaModMascota" value="<?php echo htmlspecialchars($razaData); ?>"  required>
      </aside>
    </article>
    <!--Fecha de nacimiento-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
      <aside class="col-sm-6">
        <input type="Date" class="form-control" name="nacimientoModMascota" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($nacimientoData); ?>"  required>
      </aside>
    </article>
    <!--Historial-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Historial:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="historialModMascota" value="<?php echo htmlspecialchars($historialData); ?>"  >
      </aside>
    </article>
    <!--Boton-->
    <article class="col-auto">
      <button type="submit" name="submitModMascota" class="btn btn-info mb-3" >Modificar mascota</button>
    </article>
  </form>
  </section>
</main>
<?php
include_once 'footer.php';
?>