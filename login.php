<?php
include_once 'Encabezado.php';
include_once 'menu.php';
include_once 'conexion.php';
if (isset($_SESSION['email'])) {
  // Si no tiene sesión o no es admin, redirigir al login
  header("Location: index.php");
  exit();
}

if(!empty($_POST)) {
  $email = mysqli_real_escape_string($conexion, $_POST['emailLogin']);
  $password = mysqli_real_escape_string($conexion, $_POST['passLogin']);
  $sqlLogin = "SELECT * FROM usuarios
          WHERE email='$email' AND pass = '$password' ";
  $resultadoLogin = $conexion->query($sqlLogin);
  $filas = $resultadoLogin->num_rows;
  if($filas > 0) {
    $fila = $resultadoLogin->fetch_assoc();
    $_SESSION['idUsuario'] = $fila["idUsuario"];
    $_SESSION['email'] = $fila["email"];
    $_SESSION['nombreUsuario'] = $fila["nombreUsuario"];
    $_SESSION['apellidosUsuario'] = $fila["apellidosUsuario"];
    $_SESSION['telefono'] = $fila["telefono"];
    $_SESSION['DNI'] = $fila["DNI"];
    $_SESSION['rol'] = $fila["rol"];
    header("location: index.php");
    exit();
  } else {
    echo "<script>
    alert('Credenciales erróneas');
    window.location = 'Login.php';
    </script>";
  }
}

?>

<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Inicia sesión</h1>
        <h3 class="hInicio">Si no tienes una cuenta crea una <a href="crearCuenta.php">aquí</a>.</h3>
    </section>
  <section class="login">

  <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Email:</label>

      <aside class="col-sm-6">
        <input type="email" class="form-control" name="emailLogin" value="" placeholder="email@example.com" required>
      </aside>
    </article>

    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Contaseña:</label>

      <aside class="col-sm-6">
        <input type="password" class="form-control" name="passLogin" required>
      </aside>
    </article>

    <article class="col-auto">
      <button type="submit" name="submitLogin" class="btn btn-info mb-3">Login</button>

    </article>
  </form>
  </section>
  <section class="mx-extra">

  <p class="hInicio">Si no recuerdas tu contraseña clica <a href="recuperarPass.php">aquí</a>.</p>
    </section>
</main>  

<?php
include_once 'footer.php';
?>