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

//Select para mostrar los usuarios a los que puede pertenecer la mascota
$query = "SELECT idUsuario, nombreUsuario, apellidosUsuario FROM usuarios ORDER BY nombreUsuario, apellidosUsuario";
$resultado = $conexion->query($query);


if(isset($_POST["submitCrearMiMascota"])) {//Al clicar el botón lleva aquí
  if (empty($_POST['idUsuarioSeleccionado'])) { //Forzar a seleccionar usuario
    echo "<script>
      alert('Debes seleccionar un dueño para la mascota');
      window.history.back();
    </script>";
    exit();
  }

  //Valor de los inputs
  $nombre = mysqli_real_escape_string($conexion, $_POST['nombreCrearMiMascota']);
  $tipo = mysqli_real_escape_string($conexion, $_POST['tipoCrearMiMascota']);
  $raza = mysqli_real_escape_string($conexion, $_POST['razaCrearMiMascota']);
  $nacimiento = mysqli_real_escape_string($conexion, $_POST['nacimientoCrearMiMascota']);
  $historial = "";
  //idUsuario forzadoa que si o si sea un int
  $idUsuarioMascota = intval($_POST['idUsuarioSeleccionado']);
  
  //Comprobar si ese usuario tiene una mascota con ese nombre
  $sqlMascota="SELECT nombreMascota FROM mascotas WHERE nombreMascota = '$nombre' and idUsuario = '$idUsuarioMascota'";
  $resultadoMascota = $conexion->query($sqlMascota);
  $filas = $resultadoMascota->num_rows;
  if($filas > 0) { //Si ya existe salta alert
    echo "<script>
      alert('La mascota ya existe');
      window.location = 'gestion.php';
    </script>";
  } else { //Si no existe se hace el insert
    $sqlNuevaMascota = "INSERT INTO mascotas(nombreMascota, tipoAnimal, raza, fechaNacimiento, Historial, idUsuario) 
    VALUES ('$nombre', '$tipo', '$raza', '$nacimiento', '$historial', '$idUsuarioMascota')";
    $resultadoNuevaMascota = $conexion->query($sqlNuevaMascota);
    if ($resultadoNuevaMascota) { 
      echo "<script> 
      alert('Mascota creada con éxito');
      window.location = 'gestion.php';
      </script>";
    } else {//Si no te mantiene en la misma pagina
      echo "<script>
      alert('Error al crear la mascota');
      window.location = 'crearNuevaMascota.php';
      </script>";
    }
  }
}
?>

<main class="contenido-principal">
  <section class="mx-extra">
    <h1 class="hInicio">Crea una mascota</h1>
  </section>
  <section class="login">
    <!--Formulario: Action a la misma pagina-->
    <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
      <!--Dueño-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label" for="filtroUsuarios">Dueño:</label>
        <aside class="col-sm-6">
          <!-- Filtro del input: filtroFormMascotas.js -->
          <input type="text" id="filtroUsuarios" placeholder="Buscar usuario por nombre o apellidos..." class="form-control mb-2" onkeyup="filtrarOpciones()">
          <select name="idUsuarioSeleccionado" id="selectorUsuarios" class="form-select" aria-label=" select example" required >
            <option value="" disabled selected>-- Elige un usuario --</option>
            <?php
              if ($resultado && $resultado->num_rows > 0) {
                while ($usuario = $resultado->fetch_assoc()) {
                  $id = $usuario['idUsuario'];
                  $nombreCompleto = htmlspecialchars($usuario['nombreUsuario'] . ' ' . $usuario['apellidosUsuario']);
                  echo "<option value=\"$id\">$nombreCompleto</option>";
                }
              } else {
                echo "<option disabled>No hay usuarios disponibles</option>";
              }
            ?>
          </select>
        </aside>
      </article>

      <!--Nombre-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Nombre:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="nombreCrearMiMascota" value=""  required>
        </aside>
      </article>

      <!--Tipo-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Tipo:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="tipoCrearMiMascota" value="" placeholder="Perro, gato.."  required>
        </aside>
      </article>

      <!--Raza-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Raza:</label>
        <aside class="col-sm-6">
          <input type="text" class="form-control" name="razaCrearMiMascota" value=""  required>
        </aside>
      </article>

      <!--Fecha de nacimiento-->
      <article class="mb-3 row">
        <label class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
        <aside class="col-sm-6">
          <input type="date" class="form-control" name="nacimientoCrearMiMascota" max="<?php echo date('Y-m-d'); ?>"  required>
        </aside>
      </article>

      <!--Boton-->
      <article class="col-auto">
        <button type="submit" name="submitCrearMiMascota" class="btn btn-info mb-3">Crear mascota</button>
      </article>
    </form>
  </section>
</main>
<?php
include_once 'footer.php';
?>
