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

if (isset($_REQUEST['idMascota'])) {//Recoge el id de la URL
  $idMascota = intval($_REQUEST['idMascota']); 
}else {
  echo "<script>alert('ID de mascota inválido'); window.location='miPerfil.php';</script>";
  exit();
}
//Select para mostrar la mascota con ese id
$sql = "SELECT * FROM mascotas WHERE idMascota = $idMascota";
$resultado = $conexion->query($sql);
if ($resultado && $resultado->num_rows > 0) {
    $mascota = $resultado->fetch_assoc();
    //Los resultados de la select
    $historial= $mascota['Historial'];
}else {
  echo "<script>alert('Mascota no encontrada o no tienes permiso'); window.location='miPerfil.php';</script>";
  exit();
}
if (isset($_POST['submitModMiMascota'])) {//Al clicar el botón lleva aquí
  //Valor de los inputs
  $nombre = mysqli_real_escape_string($conexion, $_POST['nombreModMiMascota']);
  $tipo = mysqli_real_escape_string($conexion, $_POST['tipoModMiMascota']);
  $raza = mysqli_real_escape_string($conexion, $_POST['razaModMiMascota']);
  $nacimiento = mysqli_real_escape_string($conexion, $_POST['nacimientoModMiMascota']);
  //Hacemos update con los datos nuevos recogidos
  $sqlUpdate = "UPDATE mascotas SET nombreMascota='$nombre', tipoAnimal='$tipo', raza='$raza', fechaNacimiento='$nacimiento', Historial='$historial'
                  WHERE idMascota = $idMascota AND idUsuario = {$_SESSION['idUsuario']}";
  if ($conexion->query($sqlUpdate)) {
    echo "<script>alert('Mascota actualizada correctamente'); window.location='detallesMiMascota.php?idMascota=$idMascota';</script>";
    exit();
  } else {
    echo "<script>alert('Error al actualizar la mascota');</script>";
  }
  //Datos actuales
  $mascota['nombreMascota'] = $nombre;
  $mascota['tipoAnimal'] = $tipo;
  $mascota['raza'] = $raza;
  $mascota['fechaNacimiento'] = $nacimiento;
}
?>
<main class="contenido-principal">
  <section class="mx-extra">
    <h1 class="hInicio">Modificar mi mascota</h1>
  </section>
  <section class="login">
    <!--Formulario-->
    <form class="row g-3 mx-extra" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) . '?idMascota=' . $idMascota ?>" method="POST">
      <!--Nombre-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Nombre:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="nombreModMiMascota" value="<?= htmlspecialchars($mascota['nombreMascota']) ?>" required>
        </aside>
      </article>
      <!--Tipo-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Tipo:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="tipoModMiMascota" value="<?= htmlspecialchars($mascota['tipoAnimal']) ?>" placeholder="Perro, gato.." required>
        </aside>
      </article>
      <!--Raza-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Raza:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="razaModMiMascota" value="<?= htmlspecialchars($mascota['raza']) ?>" required>
        </aside>
      </article>
      <!--Fecha de nacimiento-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
        <aside class="col-sm-6">
          <input type="date" class="form-control" name="nacimientoModMiMascota" max="<?php echo date('Y-m-d'); ?>" value="<?= htmlspecialchars($mascota['fechaNacimiento']) ?>" required>
        </aside>
      </article>
      <!--Botón-->
      <article class="col-auto">
        <button type="submit" name="submitModMiMascota" class="btn btn-info mb-3">Modificar mascota</button>
      </article>
    </form>
  </section>
</main>
<?php
include_once 'footer.php';
?>
