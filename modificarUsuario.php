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
  
if (isset($_REQUEST['idUsuario'])) {//Recoge el idMascota de la URL
  $idUsuario = intval($_REQUEST['idUsuario']);
} else {
  echo "<script>alert('ID de usuario no especificado'); window.location='gestion.php';</script>";
  exit;
}
//Select para mostrar el usuario con ese id
$sql = "SELECT * FROM usuarios WHERE idUsuario = $idUsuario";
$resultado = $conexion->query($sql);
if ($resultado && $resultado->num_rows > 0) {
    $usuarioData = $resultado->fetch_assoc();
    $nombreData= $usuarioData['nombreUsuario'];
    $apellidosData= $usuarioData['apellidosUsuario'];
    $emailData= $usuarioData['email'];
    $passwordData= $usuarioData['pass'];
    $telefonoData= $usuarioData['telefono'];
    $dniData= $usuarioData['DNI'];
    $rolData= $usuarioData['rol'];
}

if (isset($_POST['submitModUsuario'])) {//Al clicar el botón lleva aquí
    //Valor de los inputs
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombreModUsuario']);
    $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidosModUsuario']);
    $email = mysqli_real_escape_string($conexion, $_POST['emailModUsuario']);
    $password = mysqli_real_escape_string($conexion, $_POST['passwordModUsuario']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefonoModUsuario']);
    $dni = mysqli_real_escape_string($conexion, $_POST['dniModUsuario']);
    $rol = mysqli_real_escape_string($conexion, $_POST['rolModUsuario']);
    //Hacemos update con los datos nuevos recogidos
    $sqlUpdate = "UPDATE usuarios 
                  SET email='$email', pass='$password', nombreUsuario='$nombre', apellidosUsuario='$apellidos',  telefono='$telefono', DNI='$dni',  rol='$rol'
                  WHERE idUsuario = $idUsuario";
    if ($conexion->query($sqlUpdate)) {
      echo "<script>alert('Usuario actualizado correctamente'); window.location='detallesUsuarios.php?idUsuario=$idUsuario';</script>";
    } else {
      echo "<script>alert('Error al actualizar el usuario');</script>";
    }
  } else {
    //Buscar datos actuales
    $sqlSelect = "SELECT * FROM usuarios WHERE idUsuario = $idUsuario";
    $resultado = $conexion->query($sqlSelect);
    if ($resultado && $resultado->num_rows > 0) {
      $usuario = $resultado->fetch_assoc();
    } else {
      echo "<script>alert('Usuario no encontrado'); window.location='gestion.php';</script>";
      exit;
    }
  }
?>
<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Modificar usuario</h1>
    </section>
  <section class="login">
  <!--Formulario-->
  <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <!--idUsuario-->
    <input type="hidden" name="idUsuario" value="<?php echo $idUsuario; ?>">
    <!--Nombre-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Nombre:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="nombreModUsuario" value="<?php echo htmlspecialchars($nombreData); ?>"  required>
      </aside>
    </article>
    <!--Apellidos-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Apellidos:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="apellidosModUsuario" value="<?php echo htmlspecialchars($apellidosData); ?>"  required>
      </aside>
    </article>
    <!--Email-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Email:</label>
      <aside class="col-sm-6">
        <input type="email" class="form-control" name="emailModUsuario" value="<?php echo htmlspecialchars($emailData); ?>"  required>
      </aside>
    </article>
    <!--Contraseña-->
      <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Contraseña:</label>
      <aside class="col-sm-6">
        <input type="password" class="form-control" name="passwordModUsuario" value="<?php echo htmlspecialchars($passwordData); ?>"  required>
      </aside>
    </article>
    <!--Teléfono-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Teléfono:</label>
      <aside class="col-sm-6">
        <input type="number" class="form-control" name="telefonoModUsuario" value="<?php echo htmlspecialchars($telefonoData); ?>"  required>
      </aside>
    </article>
    <!--DNI-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">DNI:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="dniModUsuario" value="<?php echo htmlspecialchars($dniData); ?>"  required>
      </aside>
    </article>
    <!--Rol-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Rol:</label>
      <aside class="col-sm-6">
        <select class="form-control" name="rolModUsuario" required>
          <option value="Admin" <?php if ($rolData === 'Admin') echo 'selected'; ?>>Admin</option>
          <option value="Cliente" <?php if ($rolData === 'Cliente') echo 'selected'; ?>>Cliente</option>
        </select>
      </aside>
    </article>
    <!--Boton-->
    <article class="col-auto">
      <button type="submit" name="submitModUsuario" class="btn btn-info mb-3" >Modificar usuario</button>
    </article>
  </form>
  </section>
</main>  

<?php
include_once 'footer.php';
?>