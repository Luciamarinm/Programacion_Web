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

if(isset($_POST["submitCrearMiMascota"])) {//Al clicar el botón lleva aquí
  //Recoger los datos enviados del formulario
  $nombre = mysqli_real_escape_string($conexion, $_POST['nombreCrearMiMascota']);
  $tipo = mysqli_real_escape_string($conexion, $_POST['tipoCrearMiMascota']);
  $raza = mysqli_real_escape_string($conexion, $_POST['razaCrearMiMascota']);
  $nacimiento = mysqli_real_escape_string($conexion, $_POST['nacimientoCrearMiMascota']);
  $historial = ""; //Historial vacío por defecto
  $idUsuarioMascota = $_SESSION['idUsuario']; //Añadimos el id del usuario que tiene iniciada sesión

  //Comprobamos que no existe una mascota con ese nombre que pertenezca al mismo usuario
  $sqlMascota="SELECT nombreMascota FROM mascotas WHERE nombreMascota = '$nombre' and idUsuario = '$idUsuarioMascota'";
  $resultadoMascota = $conexion->query($sqlMascota);
  $filas = $resultadoMascota->num_rows;
  if($filas > 0) { //Si ya existe salta alert
    echo "<script>
      alert('La mascota ya existe');
      window.location = 'miPerfil';
    </script>";
  } else { //Si no se hace el insert para crear nueva mascota
    $sqlNuevaMascota = "INSERT INTO mascotas(nombreMascota, tipoAnimal, raza, fechaNacimiento, Historial, idUsuario) 
    VALUES ('$nombre', '$tipo', '$raza', '$nacimiento', '$historial', '$idUsuarioMascota')";
    $resultadoNuevaMascota = $conexion->query($sqlNuevaMascota);
    if ($resultadoNuevaMascota) { //Si se ha creado correctamente salta alert
      echo "<script>
      alert('Mascota creada con éxito');
      window.location = 'miPerfil.php';
      </script>";
    } else { //Si no se ha creado correctamente salta otro alert
      echo "<script>
      alert('Error al crear la mascota');
      window.location = 'crearMiMascota.php';
      </script>";
    }
  }
}

?>

<main class="contenido-principal">
  <section class="mx-extra">
    <h1 class="hInicio">Crea tu mascota</h1>
  </section>
  <section class="login">
    <!--Formulario: Action a la misma pagina-->
    <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
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
          <input type="Date" class="form-control" name="nacimientoCrearMiMascota" max="<?php echo date('Y-m-d'); ?>" required>
        </aside>
      </article>
      <!--Boton-->
      <article class="col-auto">
        <button type="submit" name="submitCrearMiMascota" class="btn btn-info mb-3" >Crear mascota</button>
      </article>
    </form>
  </section>
</main>  
<?php
include_once 'footer.php';
?>