<?php
include_once 'conexion.php';

$idUsuarioSesion = $_SESSION['idUsuario']; // Usuario con sesión iniciada
//Para poder ver automáticamente sin recargar la pagina, las horas disponibles
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
  header('Content-Type: application/json');
  $servicio = isset($_GET['servicio']) ? mysqli_real_escape_string($conexion, $_GET['servicio']) : '';
  $fecha = isset($_GET['fecha']) ? mysqli_real_escape_string($conexion, $_GET['fecha']) : '';
  if (!$servicio || !$fecha) {
      echo json_encode([]);
      exit();
  }
  //Horas posibles en formato HH:MM
  $horasPosibles = [
      "10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30",
      "17:00","17:30","18:00","18:30","19:00","19:30","20:00","20:30"
  ];
  //Consulta las horas ocupadas en formato (HH:MM:SS)
  $idReservaAjax = isset($_GET['idReserva']) ? intval($_GET['idReserva']) : 0;
  $sql = "SELECT hora FROM reservas WHERE servicio = '$servicio' AND fecha = '$fecha'";
  if ($idReservaAjax > 0) {
      $sql .= " AND idReserva != $idReservaAjax";
  }
  $resultadoSelect = $conexion->query($sql);
  $horasOcupadas = [];
  if ($resultadoSelect && $resultadoSelect->num_rows > 0) {
      while ($fila = $resultadoSelect->fetch_assoc()) {
          $horaSinSegundos = substr($fila['hora'], 0, 5);
          $horasOcupadas[] = $horaSinSegundos;
      }
  }
  //Calcula las horas disponibles
  $horasDisponibles = array_diff($horasPosibles, $horasOcupadas);
  echo json_encode(array_values($horasDisponibles));
  exit();
}
if (isset($_GET['ajax']) && $_GET['ajax'] == 2) {
  header('Content-Type: application/json');
  //Solo obtener mascotas del usuario de sesión
  $sqlMascotasAjax = "SELECT idMascota, nombreMascota FROM mascotas WHERE idUsuario = $idUsuarioSesion ORDER BY nombreMascota";
  $resultadoMascotasAjax = $conexion->query($sqlMascotasAjax);
  $mascotas = [];
  if ($resultadoMascotasAjax && $resultadoMascotasAjax->num_rows > 0) {
      while ($fila = $resultadoMascotasAjax->fetch_assoc()) {
          $mascotas[] = $fila;
      }
  }
  echo json_encode($mascotas);
  exit();
}
if (isset($_GET['ajax']) && $_GET['ajax'] == 3) {
  header('Content-Type: application/json');
  //Solo obtener mascotas del usuario de sesión
  $idMascotaAjax = isset($_GET['idMascota']) ? intval($_GET['idMascota']) : 0;
  if ($idMascotaAjax > 0) {
      $sqlUsuarioAjax = "SELECT usuarios.idUsuario, usuarios.nombreUsuario, usuarios.apellidosUsuario 
                         FROM usuarios 
                         INNER JOIN mascotas ON usuarios.idUsuario = mascotas.idUsuario 
                         WHERE mascotas.idMascota = $idMascotaAjax LIMIT 1";
      $resultadoUsuarioAjax = $conexion->query($sqlUsuarioAjax);
      if ($resultadoUsuarioAjax && $resultadoUsuarioAjax->num_rows > 0) {
          $usuario = $resultadoUsuarioAjax->fetch_assoc();
          echo json_encode($usuario);
      } else {
          echo json_encode([]);
      }
  } else {
      echo json_encode([]);
  }
  exit();
}

// Verifica que se haya pasado un id válido
if (isset($_REQUEST['idReserva'])) {
  $idReserva = intval($_REQUEST['idReserva']);
} else {
  echo "<script>alert('ID de reserva no especificado'); window.location='gestion.php';</script>";
  exit;
}

include_once 'menu.php';
include_once 'Encabezado.php';

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) { //Si no ha iniciado sesión redirige a index.php
  header("Location: login.php");
  exit();
}
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Cliente') {//Si no ha iniciado sesión como Cliente redirige a index.php
  header("Location: index.php");
  exit();
}
//Select para ver si la reserva existe
$sql = "SELECT * FROM reservas WHERE idReserva = $idReserva";
$resultadoSelRes = $conexion->query($sql);

//Select para ver que mascotas tiene el usuario
$sqlMascotas = "SELECT idMascota, nombreMascota FROM mascotas WHERE idUsuario = $idUsuarioSesion ORDER BY nombreMascota";
$resultadoMascotas = $conexion->query($sqlMascotas);

if ($resultadoSelRes && $resultadoSelRes->num_rows > 0) {
  $reservaData = $resultadoSelRes->fetch_assoc();
  $idUsuarioData= $reservaData['idUsuario'];
  $idMascotaData= $reservaData['idMascota'];
  $emailData= $reservaData['email'];
  $telefonoData= $reservaData['telefono'];
  $servicioData= $reservaData['servicio'];
  $fechaData= $reservaData['fecha'];
  $horaData= substr($reservaData['hora'], 0, 5);  //Quitamos los segundos
  $mensajeData= $reservaData['mensaje'];
  $diagnosticoData= $reservaData['diagnostico'];
}

if (isset($_POST['submitModReserva'])) {//Al clicar el botón lleva aquí
  $idUsuario = $idUsuarioSesion;
  //Valor de los inputs
    $idMascota = mysqli_real_escape_string($conexion, $_POST['idMascotaModReserva']);
    $servicio = mysqli_real_escape_string($conexion, $_POST['servicioModReserva']);
    $fecha = mysqli_real_escape_string($conexion, $_POST['fechaModReserva']);
    $hora = mysqli_real_escape_string($conexion, $_POST['horaModReserva']);
    $mensaje = mysqli_real_escape_string($conexion, $_POST['mensajeModReserva']);
    // Validar que la mascota pertenece al usuario en sesión
    $sqlValidarMascota = "SELECT idMascota FROM mascotas WHERE idMascota = $idMascota AND idUsuario = $idUsuarioSesion";
    $resultadoValidarMascota = $conexion->query($sqlValidarMascota);
    if (!$resultadoValidarMascota || $resultadoValidarMascota->num_rows == 0) {
        echo "<script>alert('Mascota no válida para el usuario en sesión'); window.location='reservas.php';</script>";
        exit;
    }
    //Buscamos los datos de usuario que necesitamos
    $queryUsuario = "SELECT email, telefono FROM usuarios WHERE idUsuario = $idUsuario";
    $resultadoUsuario = $conexion->query($queryUsuario);
    if ($resultadoUsuario && $resultadoUsuario->num_rows > 0) {
        $usuario = $resultadoUsuario->fetch_assoc();
        $email = $usuario['email'];
        $telefono = $usuario['telefono'];
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location='reservas.php';</script>";
        exit;
    }
    //Hacemos el update con los datos recogidos
    $sqlUpdate = "UPDATE reservas 
                  SET idUsuario='$idUsuario', idMascota='$idMascota', email='$email', telefono='$telefono', servicio='$servicio', fecha='$fecha', hora='$hora', mensaje='$mensaje', diagnostico='$diagnosticoData'
                  WHERE idReserva = $idReserva";
  
    if ($conexion->query($sqlUpdate)) {
      echo "<script>alert('Reserva actualizada correctamente'); window.location='detallesMiReserva.php?idReserva=$idReserva';</script>";
    } else {
      echo "<script>alert('Error al actualizar la reserva');</script>";
    }
} else {
    //Consulta datos actuales
    $sqlSelect = "SELECT * FROM reservas WHERE idReserva = $idReserva";
    $resultadoSR = $conexion->query($sqlSelect);
  
    if ($resultadoSR && $resultadoSR->num_rows > 0) {
      $reserva = $resultadoSR->fetch_assoc();
    } else {
      echo "<script>alert('Reserva no encontrada'); window.location='miPerfil.php';</script>";
      exit;
    }
}

?>
<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Modificar mi reserva</h1>
    </section>
  <section class="login">

  <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <!--idReserva-->
    <input type="hidden" name="idReserva" value="<?php echo $idReserva; ?>">
    <!--Usuario oculto porque no puede cambiarlo-->
    <input type="hidden" name="idUsuarioModReserva" value="<?php echo $idUsuarioSesion; ?>">
    <article class="mb-3 row">
    <?php //Nombre y apellidos del usuario
    $sqlUserSesion = "SELECT nombreUsuario, apellidosUsuario FROM usuarios WHERE idUsuario = $idUsuarioSesion";
    $resultadoUserSesion = $conexion->query($sqlUserSesion);
    $userSesion = $resultadoUserSesion->fetch_assoc();
    ?>
      <label class="col-sm-8 col-form-label">
        Usuario: <?= htmlspecialchars($userSesion['nombreUsuario'] . ' ' . $userSesion['apellidosUsuario']) ?>
      </label>
    </article>
    <!--Mascota-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Mascota:</label>
        <aside class="col-sm-6">
          <select name="idMascotaModReserva" id="selectorMascotas" class="form-select" aria-label="select example" required>
            <option value="" disabled>-- Elige una mascota --</option>
              <?php //Opciones de mascotas del usuario
              if ($resultadoMascotas && $resultadoMascotas->num_rows > 0) {
                while ($mascota = $resultadoMascotas->fetch_assoc()) {
                  $idM = $mascota['idMascota'];
                  $nombreMascota = htmlspecialchars($mascota['nombreMascota']);
                  $selected = ($idM == $idMascotaData) ? 'selected' : '';
                  echo "<option value=\"$idM\" $selected>$nombreMascota</option>";
              }
              } else {
                echo "<option disabled>No hay mascotas disponibles, crea una en tu perfil.</option>";
              }
              ?>
          </select>
        </aside>
    </article>
    <!--Servicio-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Servicio:</label>
      <aside class="col-sm-6">
        <select name="servicioModReserva" id="selectorServicios" class="form-select" aria-label="select example" required>
          <option value="" disabled <?php if (empty($servicioData)) echo 'selected'; ?>>-- Elige un servicio --</option>
          <option value="Veterinario" <?php if ($servicioData === 'Veterinario') echo 'selected'; ?>>Veterinario</option>
          <option value="Animales exóticos" <?php if ($servicioData === 'Animales exóticos') echo 'selected'; ?>>Animales exóticos</option>
          <option value="Peluquería" <?php if ($servicioData === 'Peluquería') echo 'selected'; ?>>Peluquería</option>
          <option value="Adiestramiento" <?php if ($servicioData === 'Adiestramiento') echo 'selected'; ?>>Adiestramiento</option>
          <option value="Rehabilitación" <?php if ($servicioData === 'Rehabilitación') echo 'selected'; ?>>Rehabilitación</option>
          <option value="Residencia" <?php if ($servicioData === 'Residencia') echo 'selected'; ?>>Residencia</option>
        </select>
      </aside>
    </article>
    <!--Fecha-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Fecha:</label>
      <aside class="col-sm-6">
        <input type="Date" class="form-control" name="fechaModReserva" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($fechaData); ?>"  required>
      </aside>
    </article>
    <!--Hora-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Hora:</label>
      <aside class="col-sm-6">
        <select name="horaModReserva" id="selectorHora" class="form-select" aria-label="select example" required>
          <option value="" disabled selected>-- Elige una hora disponible --</option>
        </select>
      </aside>
    </article>
    <!--Mensaje-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Mensaje:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="mensajeModReserva" value="<?php echo htmlspecialchars($mensajeData); ?>" >
      </aside>
    </article>
    <!--Boton-->
    <article class="col-auto">
      <button type="submit" name="submitModReserva" class="btn btn-info mb-3">Guardar</button>
    </article>
  </form>
</section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    //Constantes de los selectores e inputs
    const servicioSelect = document.getElementById('selectorServicios');
    const fechaInput = document.querySelector('input[name="fechaModReserva"]');
    const horaSelect = document.getElementById('selectorHora');
    const idReserva = <?php echo json_encode($idReserva); ?>;
    const servicioActual = <?php echo json_encode($servicioData); ?>;
    const fechaActual = <?php echo json_encode($fechaData); ?>;
    const horaActual = <?php echo json_encode($horaData); ?>;

    function cargarHoras() {
      const servicio = servicioSelect.value;//Valor seleccionado en el valor del input
      const fecha = fechaInput.value;//Valor seleccionado en el valor del input

      if (!servicio || !fecha) {//Reset para mostrar eso si no hay servicio o fecha seleccionada
        horaSelect.innerHTML = '<option value="" disabled selected>-- Elige una hora disponible --</option>';
        return;
      }
      //fetch para enviar parametros por url. Ajax=1
      fetch(`modificarReserva.php?ajax=1&servicio=${encodeURIComponent(servicio)}&fecha=${encodeURIComponent(fecha)}&idReserva=${idReserva}`)
        .then(response => response.json())
        .then(data => {
          horaSelect.innerHTML = '';
          if (data.length === 0) {
            horaSelect.innerHTML = '<option value="" disabled>No hay horas disponibles</option>';
            return;
          }
          data.forEach(hora => {
            const option = document.createElement('option');
            option.value = hora;
            option.textContent = hora;
            if (hora === horaActual && fecha === fechaActual && servicio === servicioActual) {
              option.selected = true;
            }
            horaSelect.appendChild(option);
          });
        })
        .catch(error => {//Catchear el error
          console.error('Error al cargar horas:', error);
        });
    }
    //EventListener para cuando cambia el valor de los elementos
    servicioSelect.addEventListener('change', cargarHoras);
    fechaInput.addEventListener('change', cargarHoras);

    //Carga las horas al inicio con los datos actuales
    if (servicioActual && fechaActual) {
      cargarHoras();
    }
  });
</script>
<?php
include_once 'footer.php';
?>