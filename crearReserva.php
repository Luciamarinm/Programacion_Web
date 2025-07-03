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
        //Quita los segundos, nos quedamos con HH:MM
        while ($fila = $resultado->fetch_assoc()) {
            $horaSinSegundos = substr($fila['hora'], 0, 5);
            $horasOcupadas[] = $horaSinSegundos;
        }
    }
    //Calcula las horas disponibles
    $horasDisponibles = array_diff($horasPosibles, $horasOcupadas);
    echo json_encode(array_values($horasDisponibles));
    exit();
}
//Para poder ver automáticamente sin recargar la pagina, los usuarios y mascotas
if (isset($_GET['ajax']) && $_GET['ajax'] == 2) {
    header('Content-Type: application/json');
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
include_once 'Encabezado.php';
include_once 'menu.php';

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
//Select para mostrar las mascotas de los usuarios
$sqlMascotas = "SELECT idMascota, nombreMascota FROM mascotas ORDER BY nombreMascota";
$resultadoMascotas = $conexion->query($sqlMascotas);

if (isset($_POST["submitMiReserva"])) {//Al clicar el botón lleva aquí
    $idUsuarioSeleccionado = isset($_POST['idUsuarioSeleccionado']) ? intval($_POST['idUsuarioSeleccionado']) : 0;
    //Limitamos los resultados a 1
    $sqlDatos = "SELECT email, telefono FROM usuarios WHERE idUsuario = $idUsuarioSeleccionado LIMIT 1";
    $resultadoDatos = $conexion->query($sqlDatos);
        $filaDatos = $resultadoDatos->fetch_assoc();
        $emailUsuario = $filaDatos['email'];
        $telefonoUsuario = $filaDatos['telefono'];
    //Valor de los inputs
    $idMascota = isset($_POST['idMascotaSeleccionada']) ? intval($_POST['idMascotaSeleccionada']) : 0;
    $servicio = isset($_POST['servicios']) ? mysqli_real_escape_string($conexion, $_POST['servicios']) : '';
    $fecha = isset($_POST['fechaReserva']) ? mysqli_real_escape_string($conexion, $_POST['fechaReserva']) : '';
    $hora = isset($_POST['horaReserva']) ? mysqli_real_escape_string($conexion, $_POST['horaReserva']) : '';
    $mensaje = isset($_POST['mensajeReserva']) ? mysqli_real_escape_string($conexion, $_POST['mensajeReserva']) : '';

    if (!$idUsuarioSeleccionado) { //Forzamos a seleccionar
        echo "<script>alert('Debes seleccionar un usuario');window.history.back();</script>";
        exit();
    }
    if (!$idMascota) {//Forzamos a seleccionar
        echo "<script>alert('Debes seleccionar una mascota');window.history.back();</script>";
        exit();
    }
    if (!$servicio) {//Forzamos a seleccionar
        echo "<script>alert('Debes seleccionar un servicio');window.history.back();</script>";
        exit();
    }
    if (!$fecha || strtotime($fecha) < strtotime(date("Y-m-d"))) {//Forzamos a seleccionar y limitamos la fecha
        echo "<script>alert('No puedes reservar en una fecha pasada');window.history.back();</script>";
        exit();
    }
    if (!$hora) {//Forzamos a seleccionar
        echo "<script>alert('Debes seleccionar una hora');window.history.back();</script>";
        exit();
    }
//Guardamos el campo diagnostico como vacío al inicio
    $diagnostico = "";
    //Revisamos que no haya una reserva con esas características
    $sqlReserva = "SELECT idReserva FROM reservas WHERE fecha = '$fecha' AND hora = '$hora' AND servicio = '$servicio'";
    $resultadoReserva = $conexion->query($sqlReserva);
    $filas = $resultadoReserva->num_rows;

    if ($filas > 0) { //Si existe salta alert
        echo "<script>alert('La reserva ya existe');window.location = 'gestion.php';</script>";
    } else { //Si no se hace el insert
        $sqlNuevaReserva = "INSERT INTO reservas(idUsuario, idMascota, email, telefono, servicio, fecha, hora, mensaje, diagnostico) 
        VALUES ('$idUsuarioSeleccionado', '$idMascota', '$emailUsuario', '$telefonoUsuario', '$servicio', '$fecha', '$hora', '$mensaje', '$diagnostico')";
        $resultadoNuevaReserva = $conexion->query($sqlNuevaReserva);

        if ($resultadoNuevaReserva) { //Si ha hecho con éxito ir a gestión.php
            echo "<script>alert('Reserva creada con éxito');window.location = 'gestion.php';</script>";
        } else {
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
            <!--Usuarios-->
            <article class="mb-3 row">
                <label class="col-sm-2 col-form-label" for="filtroUsuarios">Usuario:</label>
                <aside class="col-sm-6">
                    <!--Buscador de Usuarios-->
                    <input type="text" id="filtroUsuarios" placeholder="Buscar usuario por nombre o apellido..." class="form-control mb-2" onkeyup="filtrarOpciones()">
                    <select name="idUsuarioSeleccionado" id="selectorUsuarios" class="form-select" aria-label=" select example" required>
                        <option value="" disabled selected>-- Elige un usuario --</option>
                        <!--Logica mostrar datos usuarios en el select-->
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
            <!--Mascotas-->
            <article class="mb-3 row">
                <label class="col-sm-2 col-form-label">Mascota:</label>
                <aside class="col-sm-6">
                    <select name="idMascotaSeleccionada" id="selectorMascotas" class="form-select" aria-label="select example" required>
                        <option value="" disabled selected>-- Elige una mascota --</option>
                        <!--Logica mostrar mascotas de los usuarios en el select-->
                        <?php
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
            <!--Fecha-->
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
            <article class="mb-3 row">
                <aside class="col-sm-8">
                    <button type="submit" class="btn btn-primary" name="submitMiReserva">Reservar cita</button>
                </aside>
            </article>
        </form>
    </section>
</main>

<script>
    //Filtro de usuarios con selector
    function filtrarOpciones() {
        const input = document.getElementById('filtroUsuarios');
        const filter = input.value.toLowerCase();
        const select = document.getElementById('selectorUsuarios');
        const options = select.options;
        for (let i = 0; i < options.length; i++) {
            const txtValue = options[i].text.toLowerCase();
            options[i].style.display = txtValue.includes(filter) ? '' : 'none';
        }
    }
    //Constantes de los selectores
    const selectorUsuarios = document.getElementById('selectorUsuarios');
    const selectorMascotas = document.getElementById('selectorMascotas');
    const selectorServicios = document.getElementById('selectorServicios');
    const selectorFecha = document.querySelector('input[name="fechaReserva"]');
    const selectorHora = document.getElementById('selectorHora');
    
    let actualizandoDesdeMascota = false;
    selectorUsuarios.addEventListener('change', () => { //Cuando se cambia de usuario
        if (actualizandoDesdeMascota) return;
        const idUsuario = selectorUsuarios.value;
        if (!idUsuario) { //Si no hay usuario seleccionado
            selectorMascotas.innerHTML = '<option value="" disabled selected>-- Elige una mascota --</option>';
            return;
        }
        fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?ajax=2&idUsuario=${idUsuario}&t=${Date.now()}`)
            .then(res => res.json())
            .then(mascotas => {
                selectorMascotas.innerHTML = '<option value="" disabled selected>-- Elige una mascota --</option>';
                if (mascotas.length === 0) { //Si el usuario no tiene mascotas
                    selectorMascotas.innerHTML = '<option disabled>No hay mascotas para este usuario</option>';
                } else {
                    mascotas.forEach(mascota => {//Si el usuario tiene mascotas se añaden options con los valores
                        const option = document.createElement('option');
                        option.value = mascota.idMascota;
                        option.textContent = mascota.nombreMascota;
                        selectorMascotas.appendChild(option);
                    });
                }
            });
    }); 
    selectorMascotas.addEventListener('change', () => {//Cuando se elige mascota antes que usuario
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
    function cargarHorasDisponibles() { //Lógica de horas disponibles 
        //Selectores fecha y servicio
        const servicio = selectorServicios.value;
        const fecha = selectorFecha.value;
        if (!servicio || !fecha) { //Si uno de los campos no está seleccionado, el selector muestra solo este option
            selectorHora.innerHTML = '<option value="" disabled selected>-- Elige una hora disponible --</option>';
            return;
        }
        //fetch para enviar parametros por url. Ajax=1
        fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?ajax=1&servicio=${encodeURIComponent(servicio)}&fecha=${encodeURIComponent(fecha)}&t=${Date.now()}`)
            .then(res => res.json())
            .then(horas => {
                selectorHora.innerHTML = '<option value="" disabled selected>-- Elige una hora disponible --</option>';
                if (horas.length === 0) {
                    selectorHora.innerHTML = '<option disabled>No hay horas disponibles</option>';
                } else {
                    horas.forEach(hora => {//Por cada hora crear un option con el valor y texto
                        const option = document.createElement('option');
                        option.value = hora;
                        option.textContent = hora;
                        selectorHora.appendChild(option);
                    });
                }
            });
    }
    //EventListener para cuando cambia el valor de los elementos
    selectorServicios.addEventListener('change', cargarHorasDisponibles);
    selectorFecha.addEventListener('change', cargarHorasDisponibles);
</script>
<?php
include_once 'footer.php';
?>
