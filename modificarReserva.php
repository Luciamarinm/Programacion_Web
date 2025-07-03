<?php
include_once 'conexion.php';
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
  $idUsuarioAjax = isset($_GET['idUsuario']) ? intval($_GET['idUsuario']) : 0;
  if ($idUsuarioAjax > 0) {
      $sqlMascotasAjax = "SELECT idMascota, nombreMascota FROM mascotas WHERE idUsuario = $idUsuarioAjax ORDER BY nombreMascota";
      $resultadoMascotasAjax = $conexion->query($sqlMascotasAjax);
      $mascotas = [];
      if ($resultadoMascotasAjax && $resultadoMascotasAjax->num_rows > 0) {
          while ($fila = $resultadoMascotasAjax->fetch_assoc()) {
              $mascotas[] = $fila;
          }
      }
      echo json_encode($mascotas);
  } else {
      echo json_encode([]);
  }
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
//Recoge el id de la URL
if (isset($_REQUEST['idReserva'])) {
  $idReserva = intval($_REQUEST['idReserva']);
} else {
  echo "<script>alert('ID de reserva no especificado'); window.location='detallesReserva.php?idReserva=$idReserva';</script>";
  exit;
}

include_once 'menu.php';
include_once 'Encabezado.php';

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) { //Si no ha iniciado sesión redirige a index.php
  header("Location: login.php");
  exit();
}
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Admin') {//Si no ha iniciado sesión como Admin redirige a index.php
  header("Location: index.php");
  exit();
}

//Select para saber el nombre y apellidos de los usuarios
$query = "SELECT idUsuario, nombreUsuario, apellidosUsuario FROM usuarios ORDER BY nombreUsuario, apellidosUsuario";
$resultado = $conexion->query($query);
//Select para mostrar la reserva con ese id
$sql = "SELECT * FROM reservas WHERE idReserva = $idReserva";
$resultadoSelRes = $conexion->query($sql);
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
//Select para ver que mascotas tiene el usuario
$sqlMascotas = "SELECT idMascota, nombreMascota FROM mascotas WHERE idUsuario = $idUsuarioData ORDER BY nombreMascota";
$resultadoMascotas = $conexion->query($sqlMascotas);
if (isset($_POST['submitModReserva'])) {//Al clicar el botón lleva aquí
    //Valor de los inputs
    $idUsuario = mysqli_real_escape_string($conexion, $_POST['idUsuarioModReserva']);
    $idMascota = mysqli_real_escape_string($conexion, $_POST['idMascotaModReserva']);
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
    $servicio = mysqli_real_escape_string($conexion, $_POST['servicioModReserva']);
    $fecha = mysqli_real_escape_string($conexion, $_POST['fechaModReserva']);
    $hora = mysqli_real_escape_string($conexion, $_POST['horaModReserva']);
    $mensaje = mysqli_real_escape_string($conexion, $_POST['mensajeModReserva']);
    $diagnostico = mysqli_real_escape_string($conexion, $_POST['diagnosticoModReserva']);
    //Hacemos el update con los datos recogidos
    $sqlUpdate = "UPDATE reservas 
                  SET idUsuario='$idUsuario', idMascota='$idMascota', email='$email', telefono='$telefono', servicio='$servicio', fecha='$fecha', hora='$hora', mensaje='$mensaje', diagnostico='$diagnostico'
                  WHERE idReserva = $idReserva";
  
    if ($conexion->query($sqlUpdate)) {
      echo "<script>alert('Reserva actualizada correctamente'); window.location='detallesReserva.php?idReserva=$idReserva';</script>";
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
      echo "<script>alert('Reserva no encontrada'); window.location='gestion.php';</script>";
      exit;
    }
}

?>
<main class="contenido-principal">
<section class="mx-extra">
        <h1 class="hInicio">Modificar reserva</h1>
    </section>
  <section class="login">

  <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
    <!--idReserva-->
    <input type="hidden" name="idReserva" value="<?php echo $idReserva; ?>">
    <!--Usuario-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label" for="filtroUsuarios">Usuario:</label>
        <aside class="col-sm-6">
          <input type="text" id="filtroUsuarios" placeholder="Buscar usuario por nombre o apellido..." class="form-control mb-2" onkeyup="filtrarOpciones()">
          <select name="idUsuarioModReserva" id="selectorUsuarios" class="form-select" aria-label=" select example" required>
            <option value="" disabled selected>-- Elige un usuario --</option>
              <?php
                if ($resultado && $resultado->num_rows > 0) {
                  while ($usuario = $resultado->fetch_assoc()) {
                    $id = $usuario['idUsuario'];
                    $nombreCompleto = htmlspecialchars($usuario['nombreUsuario'] . ' ' . $usuario['apellidosUsuario']);
                    $selected = ($id == $idUsuarioData) ? 'selected' : '';
                    echo "<option value=\"$id\" $selected>$nombreCompleto</option>";
                }
                } else {
                  echo "<option disabled>No hay usuarios disponibles</option>";
                }
              ?>
          </select>
        </aside>
    </article>
    <!--Mascota-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Mascota:</label>
        <aside class="col-sm-6">
          <select name="idMascotaModReserva" id="selectorMascotas" class="form-select" aria-label="select example" required>
            <option value="" disabled selected>-- Elige una mascota --</option>
              <?php
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
        <!--Selector con options-->
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
    <!--Diagnóstico-->
    <article class="mb-3 row">
      <label class="col-sm-2 col-form-label">Diagnóstico:</label>
      <aside class="col-sm-6">
        <input type="text" class="form-control" name="diagnosticoModReserva"  value="<?php echo htmlspecialchars($diagnosticoData); ?>">
      </aside>
    </article>
    <!--Boton-->
    <article class="col-auto">
      <button type="submit" name="submitModReserva" class="btn btn-info mb-3" >Modificar reserva</button>
    </article>
  </form>
  </section>
</main>  
<script>
  //Guardamos la hora que recogemos
  const horaSeleccionada = "<?php echo $horaData; ?>";
  //Función de filtra los options del select de usuarios
    function filtrarOpciones() {
        const input = document.getElementById('filtroUsuarios');
        const filter = input.value.toLowerCase();
        const select = document.getElementById('selectorUsuarios');
        const options = select.options;
        //Recorremos las opciones y las mostramos u ocultamos
        for (let i = 0; i < options.length; i++) {
            const txtValue = options[i].text.toLowerCase();
            options[i].style.display = txtValue.includes(filter) ? '' : 'none';
        }
    }
    //Constantes de los selectores e inputs
    const selectorUsuarios = document.getElementById('selectorUsuarios');
    const selectorMascotas = document.getElementById('selectorMascotas');
    const selectorServicios = document.getElementById('selectorServicios');
    const selectorFecha = document.querySelector('input[name="fechaModReserva"]');
    const selectorHora = document.getElementById('selectorHora');
    let actualizandoDesdeMascota = false;
    //Evento para los cambios de selección de usuarios
    selectorUsuarios.addEventListener('change', () => {
        if (actualizandoDesdeMascota) return;
        const idUsuario = selectorUsuarios.value;
        if (!idUsuario) {
            selectorMascotas.innerHTML = '<option value="" disabled selected>-- Elige una mascota --</option>';
            return;
        }
        //Mascotas del usuario
        fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?ajax=2&idUsuario=${idUsuario}&t=${Date.now()}`)
            .then(res => res.json())
            .then(mascotas => {
                selectorMascotas.innerHTML = '<option value="" disabled selected>-- Elige una mascota --</option>';
                if (mascotas.length === 0) { //Si no hay mascotas
                    selectorMascotas.innerHTML = '<option disabled>No hay mascotas para este usuario</option>';
                } else { //Si hay las añadimos
                    mascotas.forEach(mascota => {
                        const option = document.createElement('option');
                        option.value = mascota.idMascota;
                        option.textContent = mascota.nombreMascota;
                        selectorMascotas.appendChild(option);
                    });
                }
            });
    });
    //Evento para los cambios de selección de mascotas
    selectorMascotas.addEventListener('change', () => {
        const idMascota = selectorMascotas.value;
        if (!idMascota) return;
        fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?ajax=3&idMascota=${idMascota}&t=${Date.now()}`)
            .then(res => res.json())
            .then(usuario => {
                if (usuario && usuario.idUsuario) {
                    actualizandoDesdeMascota = true;
                    for (let i = 0; i < selectorUsuarios.options.length; i++) {
                        if (selectorUsuarios.options[i].value == usuario.idUsuario) {
                            selectorUsuarios.selectedIndex = i;
                            break;
                        }
                    }
                    actualizandoDesdeMascota = false;
                }
            });
    });

    function cargarHoras() {
    const servicio = selectorServicios.value;//Valor seleccionado en el valor del input
    const fecha = selectorFecha.value;//Valor seleccionado en el valor del input

    if (!servicio || !fecha) {//Reset para mostrar eso si no hay servicio o fecha seleccionada
        selectorHora.innerHTML = '<option value="" disabled selected>-- Elige una hora disponible --</option>';
        return;
    }
    fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?ajax=1&idReserva=<?php echo $idReserva; ?>&servicio=${encodeURIComponent(servicio)}&fecha=${encodeURIComponent(fecha)}&t=${Date.now()}`)
    .then(res => res.json())
        .then(horas => {
            selectorHora.innerHTML = '<option value="" disabled>-- Elige una hora disponible --</option>';
            if (horas.length === 0) {
                selectorHora.innerHTML += '<option disabled>No hay horas disponibles</option>';
            } else {
                horas.forEach(hora => {
                    const option = document.createElement('option');
                    option.value = hora;
                    option.textContent = hora;
                    if (hora === horaSeleccionada) {
                        option.selected = true;
                    }
                    selectorHora.appendChild(option);
                });
            }
        });
    }
    //EventListener para cuando cambia el valor de los elementos
    selectorServicios.addEventListener('change', cargarHoras);
    selectorFecha.addEventListener('change', cargarHoras);
    window.addEventListener('DOMContentLoaded', cargarHoras);
</script>
<?php
include_once 'footer.php';
?>