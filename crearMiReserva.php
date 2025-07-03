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
    $sql = "SELECT hora FROM reservas WHERE servicio = '$servicio' AND fecha = '$fecha'";
    $resultado = $conexion->query($sql);
    $horasOcupadas = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            //Quita los segundos, nos quedamos con HH:MM
            $horaSinSegundos = substr($fila['hora'], 0, 5); 
            $horasOcupadas[] = $horaSinSegundos;
        }
    }
    //Calcula las horas disponibles
    $horasDisponibles = array_diff($horasPosibles, $horasOcupadas);
    echo json_encode(array_values($horasDisponibles));
    exit();
}
include_once 'Encabezado.php';
include_once 'menu.php';

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) { //Si no ha iniciado sesión redirige a index.php
    header("Location: login.php");
    exit();
}
//Variables de datos del usuario de la sesión
$idUsuario = $_SESSION['idUsuario'];
$emailUsuario = $_SESSION['email'];
$telefonoUsuario = $_SESSION['telefono'];

//Select para ver que mascotas tiene el usuario
$sqlMascotas = "SELECT idMascota, nombreMascota FROM mascotas WHERE idUsuario = '$idUsuario' ORDER BY nombreMascota";
$resultadoMascotas = $conexion->query($sqlMascotas);

if (isset($_POST["submitMiReserva"])) {//Al clicar el botón lleva aquí
      //Recoger los datos enviados del formulario
    $idMascota = isset($_POST['idMascotaSeleccionada']) ? intval($_POST['idMascotaSeleccionada']) : 0;
    $servicio = isset($_POST['servicios']) ? mysqli_real_escape_string($conexion, $_POST['servicios']) : '';
    $fecha = isset($_POST['fechaReserva']) ? mysqli_real_escape_string($conexion, $_POST['fechaReserva']) : '';
    $hora = isset($_POST['horaReserva']) ? mysqli_real_escape_string($conexion, $_POST['horaReserva']) : '';
    $mensaje = isset($_POST['mensajeReserva']) ? mysqli_real_escape_string($conexion, $_POST['mensajeReserva']) : '';
    if (!$idMascota) { //Para forzar a rellenar el campo
        echo "<script>alert('Debes seleccionar una mascota');window.history.back();</script>";
        exit();
    }
    if (!$servicio) {//Para forzar a rellenar el campo
        echo "<script>alert('Debes seleccionar un servicio');window.history.back();</script>";
        exit();
    }
    if (!$fecha || strtotime($fecha) < strtotime(date("Y-m-d"))) {//Filtro para no usar fechas anteriores a hoy
        echo "<script>alert('No puedes reservar en una fecha pasada');window.history.back();</script>";
        exit();
    }
    if (!$hora) { //Para forzar a rellenar el campo
        echo "<script>alert('Debes seleccionar una hora');window.history.back();</script>";
        exit();
    }
    $diagnostico = "";
    //Si la reserva ya existe
    $sqlReserva = "SELECT idReserva FROM reservas WHERE fecha = '$fecha' AND hora = '$hora' AND servicio = '$servicio'";
    $resultadoReserva = $conexion->query($sqlReserva);
    $filas = $resultadoReserva->num_rows;

    if ($filas > 0) {
        echo "<script>alert('La reserva ya existe');window.location = 'gestion.php';</script>";
    } else { //Si no existe insertamos la reserva
        $sqlNuevaReserva = "INSERT INTO reservas(idUsuario, idMascota, email, telefono, servicio, fecha, hora, mensaje, diagnostico) 
        VALUES ('$idUsuario', '$idMascota', '$emailUsuario', '$telefonoUsuario', '$servicio', '$fecha', '$hora', '$mensaje', '$diagnostico')";
        $resultadoNuevaReserva = $conexion->query($sqlNuevaReserva);
        if ($resultadoNuevaReserva) {
        if (isset($_SESSION['rol'])) {
            if ($_SESSION['rol'] === 'Admin') { //Si el usuario es admin, al crear la reserva lleva a gestion
                echo "<script>alert('Reserva creada con éxito');window.location = 'gestion.php';</script>";
            } elseif ($_SESSION['rol'] === 'Cliente') {//Si el usuario es cliente, al crear la reserva lleva a miPerfil
                echo "<script>alert('Reserva creada con éxito');window.location = 'miPerfil.php';</script>";
            }
          }
        } else {//Alert si no se crea por error
            echo "<script>alert('Error al crear la reserva');window.location = 'reservar.php';</script>";
        }
    }
}
?>
<main class="contenido-principal">
    <section class="mx-extra">
        <h1 class="hInicio">Reserva cita</h1>
    </section>
    <section class="login">
        <!--Formulario: Action a la misma pagina-->
        <form class="row g-3 mx-extra" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <!--Servicio-->
            <article class="mb-3 row">
                <label class="col-sm-2 col-form-label">Servicio:</label>
                <aside class="col-sm-6">
                    <select name="servicios" id="selectorServicios" class="form-select" aria-label="select example" required>
                        <option value="" disabled selected>-- Elige un servicio --</option>
                        <option value="Veterinario">Veterinario</option>
                        <option value="Animales exóticos">Animales exóticos</option>
                        <option value="Peluquería">Peluquería</option>
                        <option value="Adiestramiento">Adiestramiento</option>
                        <option value="Rehabilitación">Rehabilitación</option>
                        <option value="Residencia">Residencia</option>
                    </select>
                </aside>
            </article>
            <!--Nombre mascota-->
            <article class="mb-3 row">
                <label class="col-sm-2 col-form-label">Mi mascota:</label>
                <aside class="col-sm-6">
                    <select name="idMascotaSeleccionada" id="selectorMascotas" class="form-select" aria-label="select example" required>
                        <option value="" disabled selected>-- Elige una mascota --</option>
                        <?php //Lógica del select para elegir mascota del usuario
                        if ($resultadoMascotas && $resultadoMascotas->num_rows > 0) {
                            while ($mascota = $resultadoMascotas->fetch_assoc()) {
                                $idM = $mascota['idMascota'];
                                $nombreMascota = htmlspecialchars($mascota['nombreMascota']);
                                echo "<option value=\"$idM\">$nombreMascota</option>";
                            }
                        } else {
                            echo "<option disabled>No hay mascotas disponibles, crea una en tu perfil.</option>";
                        }
                        ?>
                    </select>
                </aside>
            </article>
            <!--Fecha de reserva-->
            <article class="mb-3 row">
                <label class="col-sm-2 col-form-label">Fecha:</label>
                <aside class="col-sm-6">
                    <input type="date" class="form-control" name="fechaReserva" min="<?php echo date('Y-m-d'); ?>" required>
                </aside>
            </article>

            <!--Hora-->
            <article class="mb-3 row">
                <label class="col-sm-2 col-form-label">Hora:</label>
                <aside class="col-sm-6">
                    <select name="horaReserva" id="selectorHora" class="form-select" aria-label="select example" required>
                        <option value="" disabled selected>-- Elige una hora disponible --</option>
                    </select>
                </aside>
            </article>

            <!--Mensaje-->
            <article class="mb-3 row">
                <label class="col-sm-2 col-form-label">Mensaje: (Opcional)</label>
                <aside class="col-sm-6">
                    <textarea class="form-control" name="mensajeReserva" rows="3"></textarea>
                </aside>
            </article>

            <!--Boton-->
            <article class="col-auto">
                <button type="submit" name="submitMiReserva" class="btn btn-info mb-3">Reservar cita</button>
            </article>
        </form>
    </section>
</main>

<script>
    //Constantes de los selectores e inputs
    const servicioSelect = document.getElementById('selectorServicios'); 
    const fechaInput = document.querySelector('input[name="fechaReserva"]');
    const horaSelect = document.getElementById('selectorHora');

    function cargarHoras() {
        const servicio = servicioSelect.value; //Valor seleccionado en el valor del input
        const fecha = fechaInput.value; //Valor seleccionado en el valor del input

        if (!servicio || !fecha) { //Reset para mostrar eso si no hay servicio o fecha seleccionada
            horaSelect.innerHTML = '<option value="" disabled selected>-- Elige una hora disponible --</option>';
            return;
        }
        //fetch para enviar parametros por url. Ajax=1
        fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?ajax=1&servicio=${encodeURIComponent(servicio)}&fecha=${encodeURIComponent(fecha)}&t=${Date.now()}`)
            .then(response => response.json())
            .then(data => {
                horaSelect.innerHTML = '<option value="" disabled selected>-- Elige una hora disponible --</option>';
                data.forEach(hora => { //Por cada hora crear un option con el valor y texto
                    const option = document.createElement('option');
                    option.value = hora;
                    option.textContent = hora;
                    horaSelect.appendChild(option);
                });
            })
            .catch(err => { //Catchear el error
                console.error('Error al cargar horas:', err);
            });
    }
    //EventListener para cuando cambia el valor de los elementos
    servicioSelect.addEventListener('change', cargarHoras); 
    fechaInput.addEventListener('change', cargarHoras);
</script>
<?php
include_once 'footer.php';
?>