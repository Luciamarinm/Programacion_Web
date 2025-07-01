<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';
if (isset($_SESSION['email'])) {
  // Si no tiene sesión o no es admin, redirigir al login
  header("Location: index.php");
  exit();
}
if(isset($_POST["submitCrearUser"])) {
  $nombre = mysqli_real_escape_string($conexion, $_POST['nombreCrearUser']);
  $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidosCrearUser']);
  $telefono = mysqli_real_escape_string($conexion, $_POST['telefonoCrearUser']);
  $dni = mysqli_real_escape_string($conexion, $_POST['dniCrearUser']);
  $email = mysqli_real_escape_string($conexion, $_POST['emailCrearUser']);
  $password = mysqli_real_escape_string($conexion, $_POST['passCrearUser']);
  $rol = "Cliente";
  $sqlUsuario="SELECT idUsuario FROM usuarios WHERE email = '$email'";
  $resultadoUsuario = $conexion->query($sqlUsuario);
  $filas = $resultadoUsuario->num_rows;
  if($filas > 0) {
    echo "<script>
      alert('El usuario ya existe');
      window.location = 'login.php';
    </script>";
  } else {
    $sqlNuevoUser = "INSERT INTO usuarios(email, pass, nombreUsuario, apellidosUsuario, telefono, DNI, rol) 
    VALUES ('$email', '$password', '$nombre', '$apellidos', '$telefono', '$dni', '$rol')";
    $resultadoUser = $conexion->query($sqlNuevoUser);
    if ($resultadoUser) {
      echo "<script>
      alert('Usuario creado con éxito');
      window.location = 'login.php';
      </script>";
    } else {
      echo "<script>
      alert('Error al crear el usuario');
      window.location = 'crearCuenta.php';
      </script>";
    }
  }

}



?>

<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Crea tu cuenta</h1>
        <h3 class="hInicio">Si ya tienes una cuenta clica <a href="login.php">aquí</a>.</h3>
    </section>
  <section class="login">

  <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <!--Nombre-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Nombre:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="nombreCrearUser" value=""  required>
      </aside>
    </article>
    <!--Apellidos-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Apellidos:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="apellidosCrearUser" value=""  required>
      </aside>
    </article>
    <!--Teléfono-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Teléfono:</label>
      <aside class="col-sm-6">
        <input type="number" class="form-control" name="telefonoCrearUser" value=""  required>
      </aside>
    </article>
    <!--DNI-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">DNI:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="dniCrearUser" value=""  required>
      </aside>
    </article>
    <!--Email-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Email:</label>
      <aside class="col-sm-6">
        <input type="email" class="form-control" name="emailCrearUser" value="" placeholder="email@example.com" required>
      </aside>
    </article>
    <!--Contaseña-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Contaseña:</label >
      <aside class="col-sm-6">
        <input type="password" class="form-control" name="passCrearUser" required>
      </aside>
    </article>
    <!--Boton-->
    <article class="col-auto">
      <button type="submit" name="submitCrearUser" class="btn btn-info mb-3" >Crear cuenta</button>
    </article>
  </form>
  </section>
</main>  

<?php
include_once 'footer.php';
?>