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

if (isset($_REQUEST['idUsuario'])) {
  $idUsuario = intval($_REQUEST['idUsuario']); 
}else {
  echo "<script>alert('ID de usuario inválido'); window.location='miPerfil.php';</script>";
  exit();
}

//Select para mostrar usuario con ese id
$sql = "SELECT * FROM usuarios WHERE idUsuario = $idUsuario";
$resultado = $conexion->query($sql);
if ($resultado && $resultado->num_rows > 0) {
  $usuario = $resultado->fetch_assoc();
  $rol = $usuario['rol'];
  $password = $usuario['pass'];
} else {
  echo "<script>alert('Usuario no encontrado o no tienes permiso'); window.location='miPerfil.php';</script>";
  exit();
}

if (isset($_POST['submitModMiUsuario'])) {//Al clicar el botón lleva aquí
  $nombre = mysqli_real_escape_string($conexion, $_POST['nombreModMiUsuario']);
  $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidosModMiUsuario']);
  $email = mysqli_real_escape_string($conexion, $_POST['emailModMiUsuario']);
  $pass = mysqli_real_escape_string($conexion, $_POST['passModMiUsuario']);
  $telefono = mysqli_real_escape_string($conexion, $_POST['telefonoModMiUsuario']);
  $DNI = mysqli_real_escape_string($conexion, $_POST['dniModMiUsuario']);
  //Hacemos update con los datos nuevos recogidos
  $sqlUpdate = "UPDATE usuarios SET email='$email', pass='$pass', nombreUsuario='$nombre', apellidosUsuario='$apellidos', telefono='$telefono', DNI='$DNI', rol='$rol' 
                WHERE idUsuario=$idUsuario";
  if ($conexion->query($sqlUpdate)) {
    echo "<script>alert('Usuario actualizad correctamente'); window.location='miPerfil.php';</script>";
    exit();
  } else {
    echo "<script>alert('Error al actualizar la mascota');</script>";
  }

  //Datos actuales
  $usuario['nombreModMiUsuario'] = $nombre;
  $usuario['apellidosModMiUsuario'] = $apellidos;
  $usuario['emailModMiUsuario'] = $email;
  $usuario['passModMiUsuario'] = $pass;
  $usuario['telefonoModMiUsuario'] = $telefono;
  $usuario['dniModMiUsuario'] = $DNI;
}

?>

<main class="contenido-principal">
  <section class="mx-extra">
    <h1 class="hInicio">Modificar mi usuario</h1>
  </section>
  <section class="login">
    <!--Formulario-->
    <form class="row g-3 mx-extra" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) . '?idUsuario=' . $idUsuario ?>" method="POST">
      <article class="mb-3 row">
        <!--Nombre-->
        <label class="col-sm-2 col-form-label">Nombre:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="nombreModMiUsuario" value="<?= htmlspecialchars($usuario['nombreUsuario']) ?>" required>
        </aside>
      </article>
      <!--Apellidos-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Apellidos:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="apellidosModMiUsuario" value="<?= htmlspecialchars($usuario['apellidosUsuario']) ?>"  required>
        </aside>
      </article>
      <!--DNI-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">DNI:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="dniModMiUsuario" value="<?= htmlspecialchars($usuario['DNI']) ?>"  required>
        </aside>
      </article>
      <!--Teléfono-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Teléfono:</label>
        <aside class="col-sm-6">
          <input type="number" class="form-control" name="telefonoModMiUsuario" value="<?= htmlspecialchars($usuario['telefono']) ?>"  required>
        </aside>
      </article>
      <!--Email-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Email:</label>
        <aside class="col-sm-6">
          <input type="email" class="form-control" name="emailModMiUsuario" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </aside>
      </article>
      <!--Contraseña-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Contraseña:</label>
        <aside class="col-sm-6">
          <input type="password" class="form-control" name="passModMiUsuario" value="<?= htmlspecialchars($password) ?>" required>
        </aside>
      </article>
      <!--Botón-->
      <article class="col-auto">
        <button type="submit" name="submitModMiUsuario" class="btn btn-info mb-3">Modificar mi usuario</button>
      </article>
    </form>
  </section>
</main>
<?php
include_once 'footer.php';
?>
